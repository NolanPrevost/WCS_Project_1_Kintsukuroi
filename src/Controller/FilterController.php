<?php

namespace App\Controller;

use App\Model\ColorManager;
use App\Model\CategoryManager;

class FilterController extends AbstractController
{
    /**
     * List colors
     */
    public function index()
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
            return $this->twig->render('Admin/filters.html.twig', [
                'colors' => $colors,
                'filtersCategories' => $filtersCategories,
                'categories' => $categories,
                'otherCategories' => $otherCategories
            ]);
        } else {
            header('Location: /');
        }
    }

    /**
     * Show informations for a specific color
     */
    public function showColorById(int $id): string
    {
        $colorManager = new ColorManager();
        $color = $colorManager->selectOneById($id);

        return $this->twig->render('Color/show.html.twig', ['color' => $color]);
    }

    /**
     * Show informations for a specific category
     */
    public function showCategoryById(int $id): string
    {
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        return $this->twig->render('Category/show.html.twig', ['category' => $category]);
    }
}
