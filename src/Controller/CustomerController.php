<?php

namespace App\Controller;

use App\Model\CustomerManager;

class CustomerController extends AbstractController
{
    /* Made by Jerome Bach */

    /**
     * List customers.
     */
    public function index(): string
    {
        if ($_SESSION['admin'] != true) {
            header('Location: /Home/accessdenied');
        }
            $customerManager = new CustomerManager();
            $customers = $customerManager->selectAll('lastname');

            return $this->twig->render('Customer/index.html.twig', ['customers' => $customers]);
    }

    /**
     * Show informations for a specific customer.
     */
    public function show(int $id): string
    {
        if ($_SESSION['admin'] != true) {
            header('Location: /Home/accessdenied');
        }
            $customerManager = new CustomerManager();
            $customer = $customerManager->selectOneById($id);
            $customerCommand = $customerManager->selectClientCommand($id);

            return $this->twig->render(
                'Customer/show.html.twig',
                ['customer' => $customer, 'customercommand' => $customerCommand]
            );
    }

    /* Hash password made by Mélissa Kintz*/

    /**
     * Edit a specific customer.
     */
    public function edit(int $id): string
    {
        if ($_SESSION['user']['id'] != $id && $_SESSION['admin'] != true) {
            header('Location: /Home/accessdenied');
        }
            $errors = [];

            $customerManager = new CustomerManager();
            $customer = $customerManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $customerEdit = array_map('trim', $_POST);
            $errors = $this->validate($customerEdit);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            if (empty($errors)) {
                if (password_verify($customerEdit['password'], $customer['password'])) {
                    $customerEdit['password'] = password_hash($customer['password'], PASSWORD_DEFAULT);
                    $customerManager->update($customer);

                    if ($_SESSION['admin'] == true) {
                        header('Location: /customer/show/' . $id);
                    } else {
                        header('Location: /Home/compte');
                    }
                } else {
                    $errors[] = "Mauvais mot de passe actuel";
                }
            }
        }

            return $this->twig->render('Customer/edit.html.twig', [
                'customer' => $customer, 'errors' => $errors,
            ]);
    }

    /**
     * Add a new customer.
     */
    public function add() //: string
    {
            $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $customer = array_map('trim', $_POST);
            $errors = $this->validate($customer);

            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            if (empty($errors)) {
                $customerManager = new CustomerManager();

                $customer['password'] = password_hash($customer['password'], PASSWORD_DEFAULT);
                $customerManager->insert($customer);
            }
            return $errors;
        }
        return $this->twig->render('Customer/add.html.twig', ['errors' => $errors]);
    }

    /**
     * Delete a specific customer.
     */
    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerManager = new CustomerManager();
            $customerManager->delete($id);
            if ($_SESSION['admin'] == true) {
                header('Location:/customer/index');
            } else {
                session_destroy();
                header('Location: /Home/index');
            }
        }
    }

    private function validate(array $customer): array
    {
        $errors = [];

        if (empty($customer['firstname'])) {
            $errors[] = 'Prénom requis';
        }
        if (empty($customer['lastname'])) {
            $errors[] = 'Nom requis';
        }
        if (empty($customer['email'])) {
            $errors[] = 'Email requis';
        }
        if (!empty($customer['email']) && !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email non valide';
        }
        if (empty($customer['phone'])) {
            $errors[] = 'Numéro de téléphone requis';
        }
        if (!empty($customer['phone']) && strlen($customer['phone']) !== 10) {
            $errors[] = 'Format du numéro de téléphone invalide';
        }

        if (empty($customer['password']) || empty($customer['passwordVerif'])) {
            $errors[] = 'Mot de passe requis';
        }
        if ($customer['password'] !== $customer['passwordVerif']) {
            $errors[] = 'Mots de passe non identiques';
        }

        return $errors ?? [];
    }


    public function indexBouquetCustomer(): string
    {
        $customerManager = new CustomerManager();
        $customers = $customerManager->selectAll('name');

        return $this->twig->render('Customer/index.html.twig', ['customers' => $customers]);
    }
}
