<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\ProductManager;
use App\Model\ColorManager;

class UserColorController extends AbstractController
{
  /**
   * List categories
   */
    public function index()
    {
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll();

        return $this->twig->render('Product/index.html.twig', ['colors' => $colors]);
    }
}
