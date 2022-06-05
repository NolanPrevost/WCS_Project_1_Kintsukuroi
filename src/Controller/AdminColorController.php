<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\ProductManager;
use App\Model\ColorManager;

class AdminColorController extends AbstractController
{
    /**
     * List categories
     */
    public function index()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $productManager = new ProductManager();
            $products = $productManager->selectAll('name');

            return $this->twig->render('Admin/filters.html.twig', ['products' => $products]);
        } else {
            header('Location: /');
        }
    }

    /**
     * Add a new color
     */
    public function addColor()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $colorManager = new ColorManager();
            $colors = $colorManager->selectAll('name');
            $categoryManager = new CategoryManager();
            $filtersCategories = $categoryManager->selectAll('type');
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
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
                $color = array_map('trim', $_POST);
                $colorInDb = $colorManager->searchColor($color);
                if (!($colorInDb)) {
                    $colorManager->insert($color);
                    header('Location: /admin/filters');
                    return null;
                } else {
                    $errors[] = "Cette couleur existe déjà.";
                }
            }
            return $this->twig->render('Admin/addColor.html.twig', [
                'colors' => $colors,
                'categories' => $categories,
                'filtersCategories' => $filtersCategories,
                'otherCategories' => $otherCategories,
                'errors' => $errors
            ]);
        } else {
            header('Location: /');
        }
    }

    /**
     * Edit a specific color
     */
    public function validateColorInDb(array $color)
    {
        $colorManager = new ColorManager();
        $color = $colorManager->selectOneById($color['id']);
        $formerColor = $color['name'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $color = array_map('trim', $_POST);
            $colorInDb = $colorManager->searchColor($color);
            if (!$colorInDb || $formerColor === $colorInDb['name']) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editColor(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $colorManager = new ColorManager();
            $color = $colorManager->selectOneById($id);
            $colors = $colorManager->selectAll('name');
            $categoryManager = new CategoryManager();
            $filtersCategories = $categoryManager->selectAll('type');
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
            $errors = [];

            if ($this->validateColorInDb($color) === true) {
                $colorManager->update($_POST);
                header('Location: /admin/filters');
                return null;
            } elseif ($this->validateColorInDb($color) === false) {
                $errors[] = "Cette couleur existe déjà.";
            }

            return $this->twig->render('Admin/editColor.html.twig', [
                'color' => $color,
                'colors' => $colors,
                'categories' => $categories,
                'filtersCategories' => $filtersCategories,
                'otherCategories' => $otherCategories,
                'errors' => $errors
            ]);
        } else {
            header('Location: /');
        }
    }

    /**
     * Delete a specific color
     */
    public function deleteColor()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $colorManager = new ColorManager();
            $colors = $colorManager->selectAll('name');
            $categoryManager = new CategoryManager();
            $filtersCategories = $categoryManager->selectAll('type');
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = trim($_POST['id']);
                $productManager = new ProductManager();
                $products = $productManager->searchByColorId($id);
                $errors = [];
                if (empty($products)) {
                    $colorManager = new ColorManager();
                    $colorManager->delete((int)$id);

                    header('Location: /admin/filters');
                } else {
                    $errors[] = 'Cette couleur ne peut pas être supprimée.
                    Elle est utilisée dans un ou plusieurs produits.';

                    return $this->twig->render('Admin/filters.html.twig', [
                        'colors' => $colors,
                        'categories' => $categories,
                        'filtersCategories' => $filtersCategories,
                        'otherCategories' => $otherCategories,
                        'errors' => $errors
                    ]);
                }
            }
        }
    }
}
