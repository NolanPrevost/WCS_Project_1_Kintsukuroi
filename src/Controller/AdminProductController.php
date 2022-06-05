<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\ProductManager;
use App\Model\ColorManager;

class AdminProductController extends AbstractController
{
    /**
     * List categories
     */
    public function index()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $productManager = new ProductManager();
            $products = $productManager->selectAll('name');
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll('type');
            $otherCategories = [];
            if (count($categories) > 5) {
                $keys = array_keys($categories);
                foreach ($keys as $key) {
                    if ($key > 3) {
                        $otherCategories[] = $categories[$key];
                        unset($categories[$key]);
                    }
                }
            }
            return $this->twig->render('Admin/products.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'otherCategories' => $otherCategories
            ]);
        } else {
            header('Location: /');
        }
    }

    public function checkAdmin()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 0) {
            header('Location: /');
        }
        return true;
    }

    /**
     * Add a new product
     */
    public function checkProduct(): string|bool
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll('name');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = array_map('trim', $_POST);
            $productManager = new ProductManager();
            $name = $_POST['name'] ? ucfirst(strtolower($_POST['name'])) : "";
            if ($productManager->isInDatabase($name)) {
                $this->errors[] = "Ce nom de produit existe déjà en base de données.";
                return $this->twig->render('Admin/addProduct.html.twig', [
                    'errors' => $this->errors, 'categories' => $categories,
                    'colors' => $colors
                ]);
            }
            if ($productManager->isEmpty($product)) {
                $this->errors[] = "Attention ! Veuillez remplir tous les champs.";
                return $this->twig->render('Admin/addProduct.html.twig', [
                    'errors' => $this->errors, 'categories' => $categories,
                    'colors' => $colors
                ]);
            }
            if (!ctype_digit($_POST['price'])) {
                $this->errors[] = "Seuls les charactères numériques sont acceptés pour le prix.";
                return $this->twig->render('Admin/addProduct.html.twig', [
                    'errors' => $this->errors, 'categories' => $categories,
                    'colors' => $colors
                ]);
            }
            if (!filter_var($_POST['image'], FILTER_VALIDATE_URL)) {
                $this->errors[] = "L'url de l'image n'est pas valide";
                return $this->twig->render('Admin/addProduct.html.twig', [
                    'errors' => $this->errors, 'categories' => $categories,
                    'colors' => $colors
                ]);
            }
            if (!ctype_digit($_POST['quantity'])) {
                $this->errors[] = "Seuls les charactères numériques sont acceptés pour la quantité.";
                return $this->twig->render('Admin/addProduct.html.twig', [
                    'errors' => $this->errors, 'categories' => $categories,
                    'colors' => $colors
                ]);
            }
            return true;
        }
        return false;
    }

    public function addProduct()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll('type');
            $colorManager = new ColorManager();
            $colors = $colorManager->selectAll('name');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $product = array_map('trim', $_POST);
                $productManager = new ProductManager();
                if ($this->checkProduct() === true) {
                    $productManager->insert($product);
                    header('Location:/admin');
                    return null;
                }
            }
            return $this->twig->render('Admin/addProduct.html.twig', [
                'errors' => $this->errors,
                'categories' => $categories,
                'colors' => $colors
            ]);
        } else {
            header('Location: /');
        }
    }

    private array $errors = [];

    /**
     * Edit a specific product
     */
    public function checkProductForEdit(int $id): string|bool
    {
        $productManager = new ProductManager();
        $product = $productManager->selectOneById($id);
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll('name');
        $formerName = $product['name'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = array_map('trim', $_POST);
            $name = $_POST['name'] ? ucfirst(strtolower($_POST['name'])) : "";

            if ($productManager->isInDatabase($name) && $name !== $formerName) {
                $this->errors[] = "Ce nom de produit existe déjà en base de données.";
                return $this->twig->render('Admin/editProduct.html.twig', [
                    'product' => $product, 'errors' => $this->errors,
                    'categories' => $categories, 'colors' => $colors
                ]);
            }
            if ($productManager->isEmpty($product)) {
                $this->errors[] = "Attention ! Veuillez remplir tous les champs.";
                return $this->twig->render('Admin/editProduct.html.twig', [
                    'product' => $product, 'errors' => $this->errors,
                    'categories' => $categories, 'colors' => $colors
                ]);
            }
            if (!filter_var($_POST['image'], FILTER_VALIDATE_URL)) {
                $this->errors[] = "L'url de l'image n'est pas valide";
                return $this->twig->render('Admin/editProduct.html.twig', [
                    'product' => $product, 'errors' => $this->errors,
                    'categories' => $categories, 'colors' => $colors
                ]);
            }
            return true;
        }
        return false;
    }

    public function editProduct(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $productManager = new ProductManager();
            $product = $productManager->selectOneById($id);
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll('type');
            $colorManager = new ColorManager();
            $colors = $colorManager->selectAll('name');
            $securityController = new SecurityController();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $product = array_map('trim', $_POST);

                if ($this->checkProductForEdit($id) === true) {
                    if ($securityController->checkDigit($id) === true) {
                        $productManager->update($product);
                        header('Location:/admin');
                        return null;
                    }
                }
            }
            return $this->twig->render('Admin/editProduct.html.twig', [
                'product' => $product, 'errors' => $this->errors,
                'categories' => $categories, 'colors' => $colors
            ]);
        } else {
            header('Location: /');
        }
    }

    public function deleteProduct(): void
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = trim($_POST['id']);
                $productManager = new ProductManager();
                $productManager->delete((int)$id);
                header('Location:/admin');
            } else {
                header('Location: /');
            }
        } else {
            header('Location: /');
        }
    }

    public function search()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $error = '';
            $productManager = new ProductManager();
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll('type');
            $otherCategories = [];
            if (count($categories) > 5) {
                $keys = array_keys($categories);
                foreach ($keys as $key) {
                    if ($key > 3) {
                        $otherCategories[] = $categories[$key];
                        unset($categories[$key]);
                    }
                }
            }
            $products = [];
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!empty($_GET['keywords'])) {
                    $keyWord = $_GET['keywords'];
                    $search = $productManager->searchByName($keyWord);
                    if (!empty($search)) {
                        $products = $search;
                    } else {
                        $error = 'Aucun produit ne correspond à votre recherche.';
                    }
                }
            }
            return $this->twig->render('Admin/products.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'otherCategories' => $otherCategories,
                'error' => $error
            ]);
        } else {
            header('Location: /');
        }
    }
}
