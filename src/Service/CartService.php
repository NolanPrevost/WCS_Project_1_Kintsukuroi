<?php

namespace App\Service;

use App\Model\ProductManager;

class CartService
{
    public function add(int $id): void
    {
        $productManager = new ProductManager();
        $product = $productManager->selectOneById(intval($id));
        $errors = [];
        if (!empty($_SESSION['cart'][$id])) {
            if ($product['quantity'] > $_SESSION['cart'][$id]) {
                $_SESSION['cart'][$id]++;
            }
            $errors[] = "Ce produit n'est plus disponible.";
        } else {
            if ($product['quantity'] >= 1) {
                $_SESSION['cart'][$id] = 1;
            }
        }

        $this->totalPrice();
        $this->countProducts();
        $this->tvaPrice();
        $this->htPrice();
    }

    public function infosCart(): array|bool
    {
        if (!empty($_SESSION['cart'])) {
            $cart = $_SESSION['cart'] ?? [];
            $cartInfos = [];
            $productManager = new ProductManager();
            foreach ($cart as $id => $qty) {
                $product = $productManager->selectOneById(intval($id));
                $product['qty_cart'] = $qty;
                $cartInfos[] = $product;
            }
            return $cartInfos;
        }
        return false;
    }

    public function delete(int $id): void
    {
        if (!empty($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]--;
            if ($_SESSION['cart'][$id] === 0) {
                unset($_SESSION['cart'][$id]);
            }
            $this->countProducts();
            $this->totalPrice();
            $this->tvaPrice();
            $this->htPrice();
        }
        header('Location: /cart');
    }

    public function countProducts(): int
    {
        $total = 0;
        $cart = $_SESSION['cart'] ?? [];
        foreach ($cart as $qty) {
            $total += $qty;
        }
        $_SESSION['nb_products'] = $total;
        return $total;
    }

    public function totalPrice(): float
    {
        $total = 0;
        $productManager = new ProductManager();
        $cart = $_SESSION['cart'] ?? [];
        foreach ($cart as $id => $qty) {
            $product = $productManager->selectOneById(intval($id));
            $total += ($product['price'] * $qty);
        }
        $_SESSION['total_price'] = $total;
        return $total;
    }

    public function tvaPrice(): float
    {
        $tva = 20;
        $total = $this->totalPrice();
        $tvaPrice = ($total * $tva) / 100;
        $_SESSION['tva_price'] = $tvaPrice;
        return $tvaPrice;
    }

    public function htPrice(): float
    {
        $total = $this->totalPrice();
        $tvaPrice = $this->tvaPrice();
        $htPrice = $total - $tvaPrice;
        $_SESSION['ht_price'] = $htPrice;
        return $htPrice;
    }
}
