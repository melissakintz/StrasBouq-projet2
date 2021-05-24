<?php

namespace App\Controller;

use App\Controller\CustomerController;
use App\Model\CustomerManager;

class ConnexionController extends AbstractController
{
    /* Made by Mélissa Kintz */

    /*
     * verification if user is already in the database
     */
    public function userExists($userTest): bool
    {
        //TODO : verifier si user existe dans la base (si son email est dans la base et/ou tel)
        $customerManager = new CustomerManager();
        $users = $customerManager->selectAll();
        $retour = true;

        foreach ($users as $user) {
            if ($userTest['email'] === $user['email']) {
                $retour =  true;
            } else {
                $retour =  false;
            }
        }
        return $retour;
    }

    /*
     * test if couple password + email exist in base
     */
    public function coupleExist($userTest)
    {
        $customerManager = new CustomerManager();
        //get id of the customer
        $id = $customerManager->selectIdByEmail($userTest['email']);
        $retour = false;

        if ($id) {
            //if there is an id , we search his informations
            $user = $customerManager->selectOneById($id['id']);

            //test if email and password matched
            if ($userTest['email'] === $user['email'] && password_verify($userTest['password'], $user['password'])) {
                $retour = true;
            }
        } else {
            $retour =  false;
        }
        return $retour;
    }

    /*
     * add a user depending of his existence in database
     */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //user unknow = add to databse
            if ($this->userExists($_POST) === false) {
                $customerController = new CustomerController();
                $errors = $customerController->add();

                if (empty($errors)) {
                    $message = "Inscription réussie, vous pouvez à présent vous connecter";
                    return $this->twig->render('Home/logIn.html.twig', ['message' => $message]);
                }
                return $this->twig->render('Home/signIn.html.twig', ['errors' => $errors]);


                //user know : any insertion in base, return message
            } elseif ($this->userExists($_POST) === true) {
                $message = 'Utilisateur connu';
                return $this->twig->render('Home/signIn.html.twig', ['message' => $message]);
            }
        }
    }

    /*
     * connection
     */
    public function connect()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //user know then redirection to home page
            if ($this->coupleExist($_POST) === true) {
                //user know :  redirection accueil

                $customerManager = new CustomerManager();
                $_SESSION['user'] = $customerManager->selectUserByEmail($_POST['email']);
                $_SESSION['login'] = $_SESSION['user']['email'];
                $this->isAdmin();
                header('Location: /Home/index');
            } else {
                //unknow then error message
                $message = 'Utilisateur inconnu, veuillez réessayer ou vous inscrire';
                return $this->twig->render('/Home/logIn.html.twig', ['message' => $message]);
            }
        }
    }


    /*
     * deconnexion session
     */
    public function deconnexion(): void
    {
        session_destroy();
        header('Location: /Home/index');
    }

    /* Made by Jerome Bach*/

    /*
     * test if admin
     */
    public function isAdmin()
    {
        define('ADMIN', 'email@admin.fr');
        if ($_SESSION['user']['email'] === ADMIN) {
            $_SESSION['admin'] = true;
        } else {
            $_SESSION['admin'] = false;
        }
    }
}
