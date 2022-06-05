<?php

namespace App\Controller;

use App\Service\CartService;

class CartController extends AbstractController
{
    public function cart(): string
    {
        $cartService = new CartService();
        $cartInfos = $cartService->infosCart();
        $cartService->countProducts();
        $cartService->totalPrice();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartService->delete(intval($_POST['delete_id']));
        }
        return $this->twig->render('Home/cart.html.twig', ['cart' => $cartInfos]);
    }
}
