<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\CategoryManager;
use App\Controller\SecurityController;

class AdminUserController extends AbstractController
{
    /**
     * List users
     */
    public function index()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $users = $userManager->selectAll();
            $categoryManager = new CategoryManager();
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
            return $this->twig->render('Admin/users.html.twig', [
                'users' => $users,
                'categories' => $categories,
                'otherCategories' => $otherCategories
            ]);
        } else {
            header('Location: /');
        }
    }

    /**
     * Search in users
     */
    public function search()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $error = '';
            $userManager = new UserManager();
            $categoryManager = new CategoryManager();
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
            $users = [];
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!empty($_GET['keywords'])) {
                    $keyWord = $_GET['keywords'];
                    $search = $userManager->searchByName($keyWord);
                    if (!empty($search)) {
                        $users = $search;
                    } else {
                        $error = 'Aucun utilisateur ne correspond à votre recherche.';
                    }
                }
            }
            return $this->twig->render('Admin/users.html.twig', [
                'users' => $users,
                'categories' => $categories,
                'otherCategories' => $otherCategories,
                'error' => $error
            ]);
        } else {
            header('Location: /');
        }
    }

    /**
     * Delete a specific user
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $userManager = new UserManager();
            $userManager->delete((int)$id);

            header('Location: /admin/users');
        } else {
            header('Location: /');
        }
    }

    /**
     * Edit a specific user
     */

    private array $errors = [];

    private function validateFields(array $user): string|bool
    {
        if (
            !empty($user['firstname']) &&
            !empty($user['lastname']) &&
            !empty($user['email']) &&
            !empty($user['address']) &&
            !empty($user['phone'])
        ) {
            $user = array_map('trim', $user);
            return true;
        } else {
            return $this->errors[] = "Veuillez remplir tous les champs";
        }
    }

    private function validateInputs(string $name1, string $name2, string $phone, string $address): string|bool
    {
        if (preg_match("/^([a-zA-Z-'À-ú ]*)$/u", $name1)) {
            if (preg_match("/^([a-zA-Z-'À-ú ]*)$/u", $name2)) {
                if (preg_match("/^([0-9]*)$/", $phone)) {
                    if (preg_match("/^([0-9a-zA-Z-'À-ú ]*)$/u", $address)) {
                        return true;
                    } else {
                        return $this->errors[] = "Adresse invalide";
                    }
                } else {
                    return $this->errors[] = "Numéro de téléphone invalide";
                }
            } else {
                return $this->errors[] = "Votre nom ne doit pas contenir de caractères spéciaux.";
            }
        } else {
            return $this->errors[] = "Votre prénom ne doit pas contenir de caractères spéciaux.";
        }
    }

    public function edit(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $securityController = new SecurityController();
            $user = $userManager->selectOneById($id);
            $formerEmail = $user['email'];
            $_POST['password'] = $user['password'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->validateFields($_POST) !== true) {
                    return $this->twig->render('Admin/editUser.html.twig', [
                        'user' => $user,
                        'errors' => $this->errors
                    ]);
                }

                if (
                    $securityController->checkChangeEmail($id) === true
                ) {
                    $firstname = $_POST['firstname'];
                    $phone = $_POST['phone'];
                    $address = $_POST['address'];
                    $lastname = $_POST['lastname'];

                    if ($this->validateInputs($firstname, $lastname, $phone, $address) === true) {
                        $userManager->update($_POST);
                        header('Location: /admin/users');
                        return null;
                    }
                } elseif (
                    $securityController->searchUserByEmail($_POST['email']) === true &&
                    $_POST['email'] !== $formerEmail
                ) {
                    $this->errors[] = "L'email est déjà utilisé";
                }
            }
            return $this->twig->render('Admin/editUser.html.twig', ['user' => $user, 'errors' => $this->errors]);
        } else {
            header('Location: /');
        }
    }
}
