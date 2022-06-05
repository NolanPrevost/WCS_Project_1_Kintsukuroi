<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\InvoiceManager;
use App\Model\InvProdManager;
use App\Service\CartService;
use App\Model\ProductManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index(): string
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();
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

        return $this->twig->render('Home/index.html.twig', [
            'categories' => $categories,
            'otherCategories' => $otherCategories,
        ]);
    }

    public function validateFields(array $orderInfos)
    {
        foreach ($orderInfos as $info) {
            if (empty($info)) {
                return false;
            }
        }
        return true;
    }

    private array $errors = [];

    public function validateInputs($name1, $name2, $address): string|bool
    {
        if (preg_match("/^([a-zA-Z-'À-ú ]*)$/", $name1)) {
            if (preg_match("/^([a-zA-Z-'À-ú ]*)$/", $name2)) {
                if (preg_match("/^([0-9a-zA-Z-'À-ú ]*)$/", $address)) {
                    return true;
                } else {
                    return $this->errors[] = "Adresse invalide";
                }
            } else {
                return $this->errors[] = "Votre nom ne doit pas contenir de caractères spéciaux.";
            }
        } else {
            return $this->errors[] = "Votre prénom ne doit pas contenir de caractères spéciaux.";
        }
    }

    public function order()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['cart'])) {
            $invoiceManager = new InvoiceManager();
            $cartService = new CartService();
            $productManager = new ProductManager();
            $invProdManager = new InvProdManager();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->validateFields($_POST) === true) {
                    $invoice = [
                        'user_id' => $_SESSION['user']['id'],
                        'created_at' => Date("y-m-d"),
                        'recipient_firstname' => $_POST['recipient_firstname'],
                        'recipient_lastname' => $_POST['recipient_lastname'],
                        'delivery_address' => $_POST['delivery_address'],
                        'payment' => $_POST['payment'],
                        'total' => $cartService->totalPrice(),
                    ];

                    if (
                        $this->validateInputs(
                            $invoice['recipient_firstname'],
                            $invoice['recipient_lastname'],
                            $invoice['delivery_address']
                        ) === true
                    ) {
                        $idInvoice = $invoiceManager->insert($invoice);
                    } else {
                        return $this->twig->render('Home/order.html.twig', ['errors' => $this->errors]);
                    }

                    foreach ($_SESSION['cart'] as $productId => $cartQuantity) {
                        $actualQuantity = $productManager->howManyInDb($productId);
                        $quantity = 0;
                        extract($actualQuantity);
                        $actualQuantity = $quantity;
                        $futureQuantity = $actualQuantity - $cartQuantity;
                        $productManager->updateQuantity($productId, $futureQuantity);

                        $newLineInTickets = [
                            'invoice_id' => $idInvoice,
                            'product_id' => $productId,
                            'quantity' => $cartQuantity,
                        ];
                        $invProdManager->insert($newLineInTickets);
                    }
                    unset($_SESSION['cart']);
                    header('Location: /confirmation');
                }
            }
            return $this->twig->render('Home/order.html.twig');
        } else {
            header('Location:/');
        }
    }

    public function orderConfirmation()
    {
        if (isset($_SESSION['user'])) {
            return $this->twig->render('Home/orderConfirmation.html.twig');
        } else {
            header('Location:/');
        }
    }
}
