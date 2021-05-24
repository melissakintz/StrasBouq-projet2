<?php

namespace App\Controller;

use App\Model\BouquetCustomerManager;
use App\Model\BouquetVitrineManager;
use App\Model\StockManager;

class PanierController extends AbstractController
{
    /* Made by Mélissa Kintz */


    /*
     * add a product to the cart
     */
    public function add(int $flowerId)
    {
        $stockManager = new StockManager();
        $flower = $stockManager->selectOneById($flowerId);

        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $exists = $this->addQuantity($flower);
        if ($exists === false) {
            $_SESSION['panier'][] = [
                $flowerId => [
                    "name" => $flower['name'],
                    "description" => $flower['description'],
                    "price" => $flower['price'],
                    "quantity" => 1,
                    "image" => $flower['image'],
                    "id" => $flower['id'],
                ],
            ];
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    /*
     * add quantity of a product
    */
    public function addQuantity(array $flower): bool
    {
        foreach ($_SESSION['panier'] as $panier => $id) {
            foreach ($id as $idf => $details) {
                if ($flower['id'] == $idf) {
                    $details = $details;
                    $flowerId = $flower['id'];
                    $_SESSION['panier'][$panier][$flowerId]['quantity'] ++;
                    $_SESSION['panier'][$panier][$flowerId]['price']
                        = $_SESSION['panier'][$panier][$flowerId]['quantity'] * $flower['price'];
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * update quantity of a product
     */
    public function updateQuantity($idFlower): void
    {
        $idFlower = (int) $idFlower;
        $stockManager = new StockManager();
        $flower = $stockManager->selectOneById($idFlower);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['+'])) {
                foreach ($_SESSION['panier'] as $panier => $id) {
                    foreach ($id as $idf => $details) {
                        if ($flower['id'] == $idf) {
                            $details = $details;
                            $flowerId = $flower['id'];
                            $_SESSION['panier'][$panier][$flowerId]['quantity'] ++;
                            $_SESSION['panier'][$panier][$flowerId]['price']
                                = $_SESSION['panier'][$panier][$flowerId]['quantity'] * (int) $flower['price'];
                        }
                    }
                }
            } elseif (isset($_POST['-'])) {
                foreach ($_SESSION['panier'] as $panier => $id) {
                    foreach ($id as $idf => $details) {
                        if ($flower['id'] == $idf) {
                            $details = $details;
                            $flowerId = $flower['id'];
                            $_SESSION['panier'][$panier][$flowerId]['quantity'] --;
                            $_SESSION['panier'][$panier][$flowerId]['price']
                                -= (int) $flower['price'];

                            if ($_SESSION['panier'][$panier][$flowerId]['quantity'] < 0) {
                                $_SESSION['panier'][$panier][$flowerId]['quantity'] = 0;
                            }
                        }
                    }
                }
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    /* Made by Jerome Bach and Maxime Becker */

    /* BON COURAGE */
    public function addBouquetVitrine(int $id)
    {
        $_SESSION['bouquet_id'] = [];
        $bouquVitrineManager = new BouquetVitrineManager();
        $bouquet = $bouquVitrineManager->showBouquetPanier($id);
        foreach ($bouquet as $flower) {
            $this->add($flower['id']);
        }
    }

    public function addBouquetCustomer(int $id)
    {
        $_SESSION['bouquet_id'] = [];
        $bouquCustomerManager = new BouquetCustomerManager();
        $bouquet = $bouquCustomerManager->showBouquetCuPanier($id);
        foreach ($bouquet as $flower) {
            $this->add($flower['id']);
        }
    }
}
