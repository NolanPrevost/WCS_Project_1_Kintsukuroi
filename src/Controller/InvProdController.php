<?php

namespace App\Controller;

use App\Model\InvProdManager;
use Exception;

class InvProdController extends AbstractController
{
    /**
     * List invprods
     */
    public function index(): string
    {
        $invProdManager = new InvProdManager();
        $invProds = $invProdManager->selectAll();

        return $this->twig->render('InvProd/index.html.twig', ['invProds' => $invProds]);
    }

    /**
     * Show informations for a specific invprod
     */
    public function show(int $id): string
    {
        $invProdManager = new InvProdManager();
        $invProds = $invProdManager->selectInvProdByInvId($id);

        return $this->twig->render('InvProd/show.html.twig', ['invProds' => $invProds]);
    }

    /**
     * Edit a specific invprod
     */
    public function edit(int $id): ?string
    {
        $invProdManager = new InvProdManager();
        $invProd = $invProdManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $invProd = array_map('trim', $_POST);

            // TODO validations (length, format...)
            if (!empty($invProd['invoice_id']) && !empty($invProd['product_id']) && !empty($invProd['quantity'])) {
                $invProdManager->update($invProd);
                header('Location:/invoice-products/show?id=' . $id);
                return null;
            } else {
                throw new Exception('The invoice_product cannot be updated.');
            }
        }

        return $this->twig->render('InvProd/edit.html.twig', [
            'invProd' => $invProd,
        ]);
    }

    /**
     * Add a new invprod
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $invProd = array_map('trim', $_POST);

            // TODO validations (length, format...)
            if (!empty($invProd['invoice_id']) && !empty($invProd['product_id']) && !empty($invProd['quantity'])) {
                $invProdManager = new InvProdManager();
                $id = $invProdManager->insert($invProd);
                header('Location:/invoice-products/show?id=' . $id);
                return null;
            } else {
                throw new Exception('The invoice_product cannot be created.');
            }
        }
        return $this->twig->render('InvProd/add.html.twig');
    }

    /**
     * Delete a specific invprod
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $invProdManager = new InvProdManager();
            $invProdManager->delete((int)$id);

            header('Location:/invoice-products');
        }
    }
}
