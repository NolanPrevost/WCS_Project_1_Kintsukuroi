<?php

namespace App\Controller;

use App\Model\InvoiceManager;
use App\Model\InvProdManager;
use App\Model\CategoryManager;
use Exception;

class InvoiceController extends AbstractController
{
    public function isAdmin(): bool
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            return true;
        }
        return false;
    }

    public function toggleTreatedNotTreated(string $url)
    {
        $invoiceManager = new InvoiceManager();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $invoiceId = $_GET;
            if ($invoiceId) {
                $invoice = $invoiceManager->selectOneById($invoiceId['id']);
                $treatmentStatus = $invoice['is_treated'];
                if ($treatmentStatus === 1) {
                    $invoiceManager->setAsTreatedInvoice($invoiceId['id'], 0);
                    header('Location: ' . $url);
                    return null;
                } elseif ($treatmentStatus === 0) {
                    $invoiceManager->setAsTreatedInvoice($invoiceId['id'], 1);
                    header('Location: ' . $url);
                    return null;
                }
            }
        }
    }

    /**
     * List all invoices
     */
    public function index()
    {
        if ($this->isAdmin() == false) {
            header('Location: /');
            return null;
        }

        $invoiceManager = new InvoiceManager();
        $invoices = $invoiceManager->selectAll();
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $otherCategories = [];

        $this->toggleTreatedNotTreated('/admin/orders');

        if (count($categories) > 5) {
            $keys = array_keys($categories);
            foreach ($keys as $key) {
                if ($key > 3) {
                    $otherCategories[] = $categories[$key];
                    unset($categories[$key]);
                }
            }
        }
        return $this->twig->render('Admin/invoices.html.twig', [
            'invoices' => $invoices,
            'categories' => $categories,
            'otherCategories' => $otherCategories
        ]);
    }

    public function treatedInvoices()
    {
        if ($this->isAdmin() == false) {
            header('Location: /');
            return null;
        }

        $invoiceManager = new InvoiceManager();
        $invoices = $invoiceManager->treatedInvoices();

        $this->toggleTreatedNotTreated('/admin/orders/treated');

        return $this->twig->render('Admin/invoices.html.twig', ['invoices' => $invoices]);
    }

    public function notTreatedInvoices()
    {
        if ($this->isAdmin() == false) {
            header('Location: /');
            return null;
        }

        $invoiceManager = new InvoiceManager();
        $invoices = $invoiceManager->notTreatedInvoices();

        $this->toggleTreatedNotTreated('/admin/orders/not-treated');

        return $this->twig->render('Admin/invoices.html.twig', ['invoices' => $invoices]);
    }

    /**
     * List invoices for user
     */
    public function userInvoices(int $id)
    {
        $invoiceManager = new InvoiceManager();
        $invoices = $invoiceManager->selectInvoicesByUser($id);

        foreach ($invoices as $invoice) {
            if (isset($_SESSION['user']) && ($_SESSION['user']['id'] === $invoice['user'])) {
                return $this->twig->render('Invoice/userInvoices.html.twig', ['invoices' => $invoices]);
            } elseif (isset($_SESSION['user']) || $_SESSION['user']['id'] !== $invoice['user']) {
                header('Location:/');
            }
        }
        return $this->twig->render('Invoice/userInvoices.html.twig');
    }

    /**
     * Show informations for a specific invoice
     */
    public function show(int $id)
    {
        $invoiceManager = new InvoiceManager();
        $invoice = $invoiceManager->selectOneById($id);

        if (
            isset($_SESSION['user']) && ($_SESSION['user']['id'] === $invoice['user']
                || $_SESSION['user']['is_admin'] === 1)
        ) {
            $invProdManager = new InvProdManager();
            $invProds = $invProdManager->selectInvProdByInvId($id);
            if (empty($invoice)) {
                header('Location:/');
            }
            $invProdManager = new InvProdManager();
            $invProds = $invProdManager->selectInvProdByInvId($id);

            return $this->twig->render('Invoice/show.html.twig', ['invoice' => $invoice, 'invProds' => $invProds]);
        } else {
            header('Location:/');
        }
    }

    /**
     * Edit a specific invoice
     */
    public function edit(int $id): ?string
    {
        $invoiceManager = new InvoiceManager();
        $invoice = $invoiceManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $invoice = array_map('trim', $_POST);

            // TODO validations (length, format...)
            if (!empty($invoice['user_id']) && !empty($invoice['created_at']) && !empty($invoice['total'])) {
                $invoiceManager->update($invoice);
                header('Location: /invoices/show?id=' . $id);
                return null;
            } else {
                throw new Exception('The invoice cannot be updated.');
            }
        }

        return $this->twig->render('Invoice/edit.html.twig', ['invoice' => $invoice]);
    }

    /**
     * Add a new invoice
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $invoice = array_map('trim', $_POST);

            // TODO validations (length, format...)
            if (!empty($invoice['user_id']) && !empty($invoice['total'])) {
                $invoiceManager = new InvoiceManager();
                $id = $invoiceManager->insert($invoice);
                header('Location:/invoices/show?id=' . $id);
                return null;
            } else {
                throw new Exception('The invoice cannot be created.');
            }
        }

        return $this->twig->render('Invoice/add.html.twig');
    }

    /**
     * Delete a specific invoice
     */
    public function delete()
    {
        if ($this->isAdmin() == false) {
            header('Location: /');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $invoiceManager = new InvoiceManager();
            $invoiceManager->delete((int)$id);

            header('Location:/admin/orders');
        }
    }
}
