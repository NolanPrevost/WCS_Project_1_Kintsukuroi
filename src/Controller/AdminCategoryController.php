<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\ProductManager;
use App\Model\ColorManager;

class AdminCategoryController extends AbstractController
{
    /**
     * Add a new category
     */
    public function addCategory()
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

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['type'])) {
                $category = array_map('trim', $_POST);
                $categoryInDb = $categoryManager->searchCategory($category);
                if (!($categoryInDb)) {
                    $categoryManager->insert($category);
                    header('Location: /admin/filters');
                    return null;
                } else {
                    $errors[] = "Cette couleur existe déjà.";
                }
            }

            return $this->twig->render('Admin/addCategory.html.twig', [
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
     * Edit a specific category
     */

    public function validateCategoryInDb(array $category)
    {
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($category['id']);
        $formerCategory = $category['type'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['type'])) {
            $category = array_map('trim', $_POST);
            $categoryInDb = $categoryManager->searchCategory($category);
            if (!($categoryInDb) || $formerCategory === $categoryInDb['type']) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editCategory(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $category = $categoryManager->selectOneById($id);
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
            $colorManager = new ColorManager();
            $colors = $colorManager->selectAll('name');
            $errors = [];

            if ($this->validateCategoryInDb($category) === true) {
                $categoryManager->update($_POST);
                header('Location: /admin/filters');
                return null;
            } elseif ($this->validateCategoryInDb($category) === false) {
                $errors[] = "Cette categorie existe déjà.";
            }

            return $this->twig->render('Admin/editCategory.html.twig', [
                'colors' => $colors,
                'category' => $category,
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
     * Delete a specific category
     */
    public function deleteCategory()
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
                $products = $productManager->searchByCategoryId($id);
                $errors = [];
                if (empty($products)) {
                    $categoryManager->delete((int)$id);
                    header('Location: /admin/filters');
                } else {
                    $errors[] = 'Cette categorie ne peut pas être supprimée.
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
        } else {
            header('Location: /');
        }
    }
}
