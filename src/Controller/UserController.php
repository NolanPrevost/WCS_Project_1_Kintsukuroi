<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Controller\SecurityController;

class UserController extends AbstractController
{
    /**
     * Show informations for a specific user
     */
    public function show(int $id)
    {
        $userManager = new UserManager();
        $user = $userManager->selectOneById($id);
        if (isset($_SESSION['user']) && ($_SESSION['user']['id'] === $user['id'])) {
            if (empty($user)) {
                header('Location:/');
            }
            return $this->twig->render('User/userShow.html.twig', ['user' => $user]);
        } else {
            header('Location:/');
        }
    }

    private array $errors = [];

    private function validateFields(array $user): string|bool
    {
        if (
            !empty($user['firstname']) &&
            !empty($user['lastname']) &&
            !empty($user['email']) &&
            !empty($user['password']) &&
            !empty($user['address']) &&
            !empty($user['phone'])
        ) {
            $user = array_map('trim', $user);
            return true;
        } else {
            return $this->errors[] = "Veuillez remplir tous les champs";
        }
    }

    public function validateInputs(string $name1, string $name2, string $phone, string $address): string|bool
    {
        if (preg_match("/^([a-zA-Z-'À-ú ]*)$/ui", $name1)) {
            if (preg_match("/^([a-zA-Z-'À-ú ]*)$/ui", $name2)) {
                if (preg_match("/^([0-9]*)$/", $phone)) {
                    if (preg_match("/^([0-9a-zA-Z-'À-ú ]*)$/ui", $address)) {
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

    /**
     * Add a new user
     */
    public function add()
    {
        if (!isset($_SESSION['user'])) {
            $userManager = new UserManager();
            $securityController = new SecurityController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->validateFields($_POST) === true) {
                    $_POST['password'] = md5($_POST['password']);
                    $_POST['passwordConf'] = md5($_POST['passwordConf']);
                    if ($securityController->searchUserByEmail($_POST['email']) === true) {
                        $this->errors[] = "Adresse email déjà enregistrée";
                    } else {
                        $firstname = $_POST['firstname'];
                        $lastname = $_POST['lastname'];
                        $phone = $_POST['phone'];
                        $address = $_POST['address'];
                        if (
                            $this->validateInputs($firstname, $lastname, $phone, $address) === true &&
                            $securityController->checkPasswords($_POST['password'], $_POST['passwordConf']) === true
                        ) {
                            $userManager->insert($_POST);
                            header('Location:/login');
                            return null;
                        }
                    }
                }
            }
            return $this->twig->render('User/add.html.twig', ['errors' => $this->errors]);
        } else {
            header('Location:/');
        }
    }

    /**
     * Edit a specific user
     */
    public function edit(int $id)
    {
        $userManager = new UserManager();
        $securityController = new SecurityController();
        $user = $userManager->selectOneById($id);
        if (isset($_SESSION['user']) && ($_SESSION['user']['id'] === $user['id'])) {
            if (empty($user)) {
                header('Location:/');
            }
            $formerEmail = $user['email'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->validateFields($_POST) !== true) {
                    return $this->twig->render('User/edit.html.twig', ['user' => $user, 'errors' => $this->errors]);
                }
                if ($securityController->checksForUserEdit($id) === true) {
                    $_POST['is_admin'] = $user['is_admin'];
                    $userManager->update($_POST);
                    header('Location: /user/show?id=' . $id);
                    return null;
                } elseif (
                    $securityController->searchUserByEmail($_POST['email']) === true &&
                    $_POST['email'] !== $formerEmail
                ) {
                    $this->errors[] = "L'email est déjà utilisé";
                }
                $this->errors = $securityController->errors;
            }
            return $this->twig->render('User/edit.html.twig', ['user' => $user, 'errors' => $this->errors]);
        } else {
            header('Location:/');
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
}
