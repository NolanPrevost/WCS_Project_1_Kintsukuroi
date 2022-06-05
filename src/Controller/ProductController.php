<?php

namespace App\Controller;

use App\Model\ProductManager;
use App\Model\CategoryManager;
use App\Model\ColorManager;
use App\Service\CartService;

class ProductController extends AbstractController
{
    /**
     * List products
     */
    public function index(): string
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAll('name');
        $cartService = new CartService();
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll('name');
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['add_product'])) {
                $product = $_POST['add_product'];
                $cartService->add($product);
            }
        }
        return $this->twig->render('Product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'colors' => $colors,
            'otherCategories' => $otherCategories
        ]);
    }

    public function search(): string
    {
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
        return $this->twig->render('Product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'otherCategories' => $otherCategories,
            'error' => $error
        ]);
    }

    public function showByColBycat()
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAllByColByCat($_GET['col'], $_GET['cat_id']);
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll('name');
        $otherCategories = [];
        if (count($categories) > 4) {
            $keys = array_keys($categories);
            foreach ($keys as $key) {
                if ($key > 3) {
                    $otherCategories[] = $categories[$key];
                    unset($categories[$key]);
                }
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartService = new CartService();
            if (!empty($_POST['add_product'])) {
                $product = $_POST['add_product'];
                $cartService->add($product);
            }
        }

        return $this->twig->render('Product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'otherCategories' => $otherCategories,
            'colors' => $colors
        ]);
    }

    /**
     * Show informations for a specific product
     */
    public function show(int $id): string
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $productManager = new ProductManager();
        $product = $productManager->selectOneById($id);
        $otherCategories = [];
        if (empty($product)) {
            header('Location:/');
        }
        if (count($categories) > 4) {
            $keys = array_keys($categories);
            foreach ($keys as $key) {
                if ($key > 3) {
                    $otherCategories[] = $categories[$key];
                    unset($categories[$key]);
                }
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartService = new CartService();
            if (!empty($_POST['add_product'])) {
                $productInCart = $_POST['add_product'];
                $cartService->add($productInCart);
            }
        }
        return $this->twig->render('Product/show.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'otherCategories' => $otherCategories,]);
    }

    /**
     * Show products by category
     */
    public function indexByCategory(int $id): string
    {
        $cartService = new CartService();
        $productManager = new ProductManager();
        $products = $productManager->selectAllByCategory($id);
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll('name');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['add_product'])) {
                $product = $_POST['add_product'];
                $cartService->add($product);
            }
        }
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);
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
        return $this->twig->render('Product/index.html.twig', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'otherCategories' => $otherCategories,
            'colors' => $colors
        ]);
    }

    /**
     * Show products by colors
     */

    public function showByColor(string $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $productManager = new ProductManager();
            $products = $productManager->searchByColorId($id);
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll('type');
            $colorManager = new ColorManager();
            $colors = $colorManager->selectAll('name');
            $otherCategories = [];
            if (count($categories) > 4) {
                $keys = array_keys($categories);
                foreach ($keys as $key) {
                    if ($key > 3) {
                        $otherCategories[] = $categories[$key];
                        unset($categories[$key]);
                    }
                }
            }

            return $this->twig->render('Product/index.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'otherCategories' => $otherCategories,
                'colors' => $colors
            ]);
        }
    }
}
