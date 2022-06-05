<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\CategoryManager;
use App\Model\ProductManager;
use App\Model\ColorManager;

class SecurityController extends AbstractController
{
    public function login()
    {
        $userManager = new UserManager();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $user = $userManager->selectOneByEmail($_POST['email']);
                if ($user) {
                    if (md5($_POST['password']) === ($user['password'])) {
                        $_SESSION['user'] = $user;
                        header('Location: /');
                    } else {
                        $errors[] = "Mot de passe invalide !";
                    }
                } else {
                    $errors[] = "L'email renseigné n'existe pas !";
                }
            } else {
                $errors[] = "Tous les champs sont requis !";
            }
        }

        return $this->twig->render('Security/login.html.twig', ['errors' => $errors]);
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /');
    }

    public array $errors = [];

    public function checkPasswords(string $password1, string $password2): bool|string
    {
        if ($password1 !== $password2) {
            return $this->errors[] = "Les mots de passe doivent être identiques.";
        }
        return true;
    }

    public function checkChangeEmail(int $id)
    {
        $userManager = new UserManager();
        $user = $userManager->selectOneById($id);
        $formerEmail = $user['email'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                $this->searchUserByEmail($_POST['email']) === false ||
                $this->searchUserByEmail($_POST['email']) === true &&
                $_POST['email'] === $formerEmail
            ) {
                return true;
            }
        }
    }

    public function validateEmail(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public function searchUserByEmail(string $email): string|bool
    {
        if ($this->validateEmail($email) === true) {
            $userManager = new UserManager();
            $user = $userManager->selectOneByEmail($email);
            if (empty($user)) {
                return false;
            } elseif ($user['email'] === $email) {
                return true;
            }
        }
        return $this->errors[] = "Adresse email invalide";
    }

    public function checksForUserEdit(int $id)
    {
        $userManager = new UserManager();
        $userController = new UserController();
        $user = $userManager->selectOneById($id);
        $formerPassword = $user['password'];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['passwordConf'] = md5($_POST['passwordConf']);
            if (
                $this->checkChangeEmail($id) === true
            ) {
                $firstname = $_POST['firstname'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];
                $lastname = $_POST['lastname'];
                if ($formerPassword !== $_POST['password']) {
                    $_POST['password'] = md5($_POST['password']);
                }
                if (
                    $userController->validateInputs($firstname, $lastname, $phone, $address) === true &&
                    $this->checkPasswords($_POST['password'], $_POST['passwordConf']) === true
                ) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function checkDigit(int $id)
    {
        $productManager = new ProductManager();
        $product = $productManager->selectOneById($id);
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('type');
        $colorManager = new ColorManager();
        $colors = $colorManager->selectAll('name');

        if (!ctype_digit($_POST['price'])) {
            $this->errors[] = "Seuls les charactères numériques sont acceptés pour le prix.";
            return $this->twig->render('Admin/editProduct.html.twig', [
                'product' => $product, 'errors' => $this->errors,
                'categories' => $categories, 'colors' => $colors
            ]);
        }
        if (!ctype_digit($_POST['quantity'])) {
            $this->errors[] = "Seuls les charactères numériques sont acceptés pour la quantité.";
            return $this->twig->render('Admin/editProduct.html.twig', [
                'product' => $product, 'errors' => $this->errors,
                'categories' => $categories, 'colors' => $colors
            ]);
        }
        return true;
    }
}
