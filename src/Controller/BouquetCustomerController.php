<?php

namespace App\Controller;

use App\Model\BouquetCustomerManager;
use App\Model\StockManager;

class BouquetCustomerController extends AbstractController
{
    /**
     * List items.
     */
    public function index(): string
    {
        if ($_SESSION['admin'] === false) {
            header('Location: /Home/accessdenied');
        }
            $bouqCustomerManager = new BouquetCustomerManager();
            $bouquetCustomers = $bouqCustomerManager->selectAll('name');

            return $this->twig->render('BouquetCustomer/index.html.twig', ['bouquetCustomers' => $bouquetCustomers]);
    }

    /**
     * Show informations for a specific item.
     */
    public function show(int $id): string
    {
        if ($_SESSION['admin'] != true) {
            header('Location: /Home/accessdenied');
        }
            $bouqCustomerManager = new BouquetCustomerManager();
            $bouquetCustomer = $bouqCustomerManager->selectOneById($id);
            $bouquet = $bouqCustomerManager->selectBouquetCustomerById($id);

            return $this->twig->render(
                'BouquetCustomer/show.html.twig',
                ['bouquet' => $bouquet, 'bouquetCustomer' => $bouquetCustomer]
            );
    }

    /**
     * Edit a specific item.
     */
    public function edit(int $id): string
    {
        $allId = [];
        $errors = [];
        $stockManager = new stockManager();
        $flowers = $stockManager->selectAll();
        $bouqCustomerManager = new BouquetCustomerManager();
        $bouquetCustomer = $bouqCustomerManager->selectOneById($id);
        $idBouquet = $bouqCustomerManager->selectOneById($id);
        $bouquet = $bouqCustomerManager->selectBouquetCustomerById($id);
        $allBouquetCustomer = $bouqCustomerManager->selectBouquetCustomer($_SESSION['user']['id']);

        foreach ($flowers as $i => $flower) {
            foreach ($bouquet as $command) {
                if ($command['stock_id'] === $flower['id']) {
                    $flowers[$i] = [];
                }
            }
        }
        foreach ($allBouquetCustomer as $bouquetId) {
            $allId[] = $bouquetId['id'];
        }

        if (in_array($id, $allId) != true) {
                        header('Location: /Home/accessdenied');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $_POST['name'] = trim($_POST['name']);
            $bouquetCustomer = $_POST;
            $errors = $this->validate($bouquetCustomer);

            if (empty($errors)) {
                $this->removeAllFlower($id);
                $bouquetCustomer['bouquet_id'] = $idBouquet['id'];
                $bouqCustomerManager->update($bouquetCustomer);

                foreach ($bouquetCustomer['stock_id'] as $idflower => $quantity) {
                    foreach ($quantity as $number) {
                        if ($number > '0') {
                            $bouquetCustomer['stock_id'] = $idflower;
                            while ((int) $number > 0) {
                                $bouqCustomerManager->insertFlowersInBouquet(
                                    $bouquetCustomer['stock_id'],
                                    $idBouquet['id']
                                );
                                $number--;
                            }
                        }
                    }
                }
                if ($_SESSION['admin'] === true) {
                    header('Location: /bouquetCustomer/show/' . $id);
                } else {
                    header('Location: /Home/compte');
                }
            }
        }

        return $this->twig->render('BouquetCustomer/edit.html.twig', [
            'bouquetCustomer' => $bouquetCustomer, 'flowers' => $flowers, 'bouquet' => $bouquet, 'errors' => $errors
        ]);
    }

    /**
     * Add a new item.
     */
    public function add(): string
    {
        if ($_SESSION['admin'] != true) {
            header('Location: /Home/accessdenied');
        }
        $errors = [];
        $stockManager = new StockManager();
        $flowers = $stockManager->selectAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data

            $_POST['name'] = trim($_POST['name']);
            $bouquetCustomer = $_POST;
            $errors = $this->validate($bouquetCustomer);

            if (empty($errors)) {
                $bouqCustomerManager = new BouquetCustomerManager();
                $bouqCustomerManager->insert($bouquetCustomer);
                $lastID = $bouqCustomerManager->selectLastId();
                $bouquetCustomer['bouquet_id'] = (int)$lastID[0];

                foreach ($bouquetCustomer['stocks'] as $idflower => $quantity) {
                    foreach ($quantity as $number) {
                        if ((int) $number > '0') {
                            $bouquetCustomer['stocks'] = $idflower;
                            while ($number > 0) {
                                $bouqCustomerManager->insertFlowersInBouquet(
                                    $bouquetCustomer['stock_id'],
                                    $bouquetCustomer['bouquet_id']
                                );
                                $number--;
                            }
                        }
                    }
                }
                if ($_SESSION['admin'] == true) {
                    header('Location:/bouquetCustomer/show/' . $bouquetCustomer['bouquet_id']);
                } else {
                    header('Location: /Home/compte');
                }
            }
        }

        return $this->twig->render('BouquetCustomer/add.html.twig', ['flowers' => $flowers, 'errors' => $errors]);
    }

    /**
     * Delete a specific item.
     */
    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bouqCustomerManager = new BouquetCustomerManager();
            $bouqCustomerManager->delete($id);
            if ($_SESSION['admin'] == true) {
                header('Location:/bouquetCustomer/index');
            } else {
                header('Location: /Home/compte');
            }
        }
    }

    /*
     * delete all flower
     */
    public function removeAllFlower($bouquetId)
    {
            $bouqCustomerManager = new BouquetCustomerManager();
            $bouqCustomerManager->deleteAllFlower($bouquetId);
            header('Location:/bouquetCustomer/edit/' . $bouquetId);
    }

    /*
     * check
     */
    private function validate(array $customer): array
    {
        $errors = [];

        if (empty($customer['name'])) {
            $errors[] = 'Nom de bouquet requis';
        }
        return $errors ?? [];
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $bouquet = $_POST;

            $bCustomer = new BouquetCustomerManager();
            $bCustomer->insert($bouquet);

            //TODO recup l'id du bouquet
            $lastID = $bCustomer->selectLastId();
            $bouquet['bouquet_id'] = (int)$lastID[0];


            //test if user is already connected
            if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                foreach ($_SESSION['panier'] as $panier => $id) {
                    $panier = $panier; //pas le choix pour sinon je peux pas commit
                    foreach ($id as $idf => $details) {
                        $bouquet['stock'][] = [
                            'stock_id' => $idf,
                            'quantity' => $details['quantity'],
                        ];
                    }
                }
                foreach ($bouquet['stock'] as $i => $flower) {
                    $i = $i; //sinon Stan n'est pas content
                    if ($flower['quantity'] > '0') {
                        $bouquet['stock'] =  $flower['stock_id'];
                        while ($flower['quantity'] > 0) {
                            $bCustomer->saveFlowersInBouquet($bouquet);
                            $flower['quantity']--;
                        }
                    }
                }
            }


                //clear the cart and redirection
                $_SESSION['panier'] = [];
                $message = "Le bouquet a bien était enregistré";
                return $this->twig->render("/Home/panier.html.twig", ['message' => $message]);
        }
            //if not connect redirection connexion page
            $message = "Veuillez vous connecter pour enregistrer le bouquet";
            return $this->twig->render("/Home/logIn.html.twig", ['message' => $message]);
    }
}
