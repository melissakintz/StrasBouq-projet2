<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\CustomerManager;
use App\Model\BouquetCustomerManager;
use App\Model\StockManager;
use App\Model\BouquetVitrineManager;

class HomeController extends AbstractController
{
    /* Administrator gestion made by Jerome Bach*/
    /*
     * Display home page
     * @return string
     */
    public function index(): string
    {
        $bouquetVitrine = new BouquetVitrineManager();
        $bouquets = $bouquetVitrine->selectAll();
        $stockFleurs = new StockManager();
        $stock = $stockFleurs->selectAll();
        return $this->twig->render('Home/index.html.twig', ['bouquet' => $bouquets, 'fleur' => $stock]);
    }

    /*
     * display cart
     */
    public function panier()
    {
        return $this->twig->render('Home/panier.html.twig');
    }

    /**
     * Display contact page
     */
    public function contact(): string
    {
        return $this->twig->render('Home/contact.html.twig');
    }

    /**
     * Display log in page
     */
    public function logIn(): string
    {
        return $this->twig->render('Home/logIn.html.twig');
    }

    /**
     * Display sign in page
     */
    public function signIn(): string
    {
        return $this->twig->render('Home/signIn.html.twig');
    }

    /*
     * display page compose ton bouquet
     */
    public function composeBouquet(): string
    {
        $stockManager = new StockManager();
        $stock = $stockManager->selectAll();
        return $this->twig->render('Home/compose.html.twig', ['stock' => $stock]);
    }

    /*
     * display page choisi ton bouquet
     */
    public function choisiBouquet(): string
    {
        $bouquetVitrine = new BouquetVitrineManager();
        $aBouquets = $bouquetVitrine->showPriceBouquet();

        return $this->twig->render('Home/choisi.html.twig', ['total' => $aBouquets]);
    }

    /*
     * display compte
     */
    public function compte(): string
    {
        if ($_SESSION['admin'] === true) {
            header('Location: /Home/compteAdmin');
        }
        $customerManager = new CustomerManager();
        $customerCommand = $customerManager->selectClientCommand($_SESSION['user']['id']);
        $bouq = [];
        $bouqCustomerManager = new BouquetCustomerManager();
        $bouquets = $bouqCustomerManager->selectBouquetCustomer($_SESSION['user']['id']);
        foreach ($bouquets as $bouquet) {
            $bouq[] = $bouqCustomerManager->selectBouquetCustomerById($bouquet['id']);
        }
        return $this->twig->render('Home/compte.html.twig', ['bouquets' => $bouquets,
        'bouq' => $bouq, 'customercommand' => $customerCommand ]);
    }

    /*
     * display page access denided
     */
    public function accessDenied(): string
    {
        return $this->twig->render('Home/accessdenied.html.twig');
    }

    /*
     * display compte admin
     */
    public function compteAdmin()
    {
        return $this->twig->render('Home/compteAdmin.html.twig');
    }
}
