<?php

namespace App\Controller;

use App\Model\CommandManager;
use App\Model\CommandStatusManager;
use App\Model\StockManager;
use App\Controller\StockController;
use App\Controller\CommandStatusController;
use App\Model\CustomerManager;

class CommandController extends AbstractController
{
    /* Made by Mélissa Kintz */


    public function index(): string
    {
        return $this->twig->render("Commande/command.html.twig");
    }

    /*
     * display edit command page
     */
    public function edit($id): string
    {
        //get flowers in order with name
        $commandManager = new CommandManager();
        $stockCommand = $commandManager->getStockCommand($id);

        //get whole stock
        $stockManager = new StockManager();
        $stock = $stockManager->selectAll();

        //if id flower already in order , clean its data
        foreach ($stock as $i => $flower) {
            foreach ($stockCommand as $command) {
                if ($command['stock_id'] === $flower['id']) {
                    $stock[$i] = [];
                }
            }
        }

        return $this->twig->render(
            "Commande/edit.html.twig",
            ["stockCommand" => $stockCommand, 'id' => $id, 'stock' => $stock]
        );
    }

    public function add()
    {
        $stockManager = new StockManager();
        $stock = $stockManager->selectAll();

        $customerManager = new CustomerManager();
        $customers = $customerManager->selectAll();

        return $this->twig->render("/Commande/addCommand.html.twig", ['stock' => $stock, 'customers' => $customers]);
    }

    /**
     * Show all informations
     */
    public function showAll(): string
    {
        $commandeManager = new CommandManager();
        $commandes = $commandeManager->selectAll();

        return $this->twig->render("Commande/addCommand.html.twig", ['commandes' => $commandes]);
    }

    /**
     * Show informations by id
     */
    public function showById(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commandeManager = new CommandManager();
            $details = $commandeManager->selectOneById($id);

            return $this->twig->render("Commande/addCommand.html.twig", ['details' => $details]);
        }
    }

    /**
     * Add a new command (with its details and status) from form
    */
    public function addCommandForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST)) {
                $commande = $_POST;
                $commande['totalAmount'] = 0;

                $stockManager = new StockManager();
                $stock = $stockManager->selectAll();


                //format date pick to fit in base
                $commande['datePick'] = $_POST['datePick'] . ' ' . $_POST['timePick'];

                //get the total amount
                foreach ($commande['stock_id'] as $stockId => $quantities) {
                    foreach ($quantities as $quantity) {
                        if (!empty($quantity) && (int)$quantity > 0) {
                            foreach ($stock as $flower) {
                                if ($flower['id'] == $stockId) {
                                    $commande['totalAmount'] += $flower['price'] * $quantity;
                                }
                            }
                        }
                    }
                }

                //insert command
                $commandeManager = new CommandManager();
                $commandeManager->insertCommand($commande);

                // TODO validations (length, format...)

                //take id of the last input in command to associate the command details and status
                $lastID = $commandeManager->selectLastId();
                $commande['command_id'] = (int)$lastID[0];


                //insert command details : for each stock_id if its quantity > 0 add a tuple
                foreach ($commande['stock_id'] as $stockId => $quantities) {
                    foreach ($quantities as $quantity) {
                        if (!empty($quantity) && (int) $quantity > 0) {
                            $commande['stock_id'] = (int)$stockId;
                            $commande['quantity'] = (int)$quantity;
                            $commandeManager->insertCommandDetails($commande);

                            //delete from the stock the flowers used for the command
                            $stockController = new StockController();
                            $stockController->decreaseAvalaibleNumber($stockId, $quantity);
                        }
                    }
                }


                // insert command status
                $commande['isPick'] = (int) $commande['isPick'];
                $commande['isPrepared'] = (int) $commande['isPrepared'];

                $commandStatusManager = new CommandStatusManager();
                $commandStatusManager->insertStatus($commande);
            }
            //redirection
            header("Location: /Command/add");
        }
    }

    /*
     * Delete a command
     */
    public function suppr(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commandeManager = new CommandManager();
            $commandeManager->delete($id);
            header("Location: /Command/showAll");
        }
    }

    /*
     * get all stock_id of one command
     */
    public function getDetails($id)
    {
        $commandManager = new CommandManager();
        $details = $commandManager->getDetails($id);

        //transform text to better comprehension for the customer or webowner
        for ($i = 1; $i < 1; $i++) {
            if ($details[$i]['isprepared'] === '0') {
                $details[$i]['isprepared'] = 'Non';
            } elseif ($details[$i]['isprepared'] === "1") {
                $details[$i]['isprepared'] = 'Oui';
            }
            if ($details[$i]['ispick'] === '0') {
                $details[$i]['ispick'] = 'Non';
            } elseif ($details[$i]['ispick'] === "1") {
                $details[$i]['ispick'] = 'Oui';
            }
        }
        return $details;
    }

    /*
     * save in bdd order the cart
     * to command via cart
     */
    public function commander()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            //test if user is already connected
            if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                foreach ($_SESSION['panier'] as $panier => $id) {
                    $panier = $panier; //pas le choix pour sinon je peux pas commit
                    foreach ($id as $idf => $details) {
                        $_SESSION['panier']['stock'][] = [
                            'stock_id' => $idf,
                            'quantity' => $details['quantity'],
                        ];
                    }
                }
                //insert data in session
                $_SESSION['panier']['datePick'] = $_POST['datePick'] . ' ' . $_POST['timePick'];
                $_SESSION['panier']['isPrepared'] = $_POST['isPrepared'];
                $_SESSION['panier']['isPick'] = $_POST['isPick'];
                $_SESSION['panier']['totalAmount'] = $_POST['totalAmount'];

                //add the command
                $this->addCommand($_SESSION['panier']);

                //clear the cart and redirection
                $_SESSION['panier'] = [];
                $message = "Merci de votre commande, celle-ci a bien été enregistrée";
                return $this->twig->render("/Home/panier.html.twig", ['message' => $message]);
            }
            //if not connect redirection connexion page
            $message = "Veuillez vous connecter pour passer commande";
            return $this->twig->render("/Home/logIn.html.twig", ['message' => $message]);
        }
    }

    /*
     * add a command (with its details and status) from cart
     */
    public function addCommand(array $commande): void
    {
        //insert command
        $commandeManager = new CommandManager();
        $commandeManager->insertCommand($commande);

        // TODO validations (length, format...)

        //take id of the last input in command to associate the command details and status
        $lastID = $commandeManager->selectLastId();
        $commande['command_id'] = (int)$lastID[0];

        //insert command details : for each stock_id insert one tuple
        foreach ($commande['stock'] as $i => $stock) {
            $i = $i; //pas le choix pour sinon je peux pas commit
            $commande['stock_id'] = (int) $stock['stock_id'];
            $commande['quantity'] = (int) $stock['quantity'];
            $commandeManager->insertCommandDetails($commande);

            //delete from the stock the flowers used for the command
            $stockController = new StockController();
            $stockController->decreaseAvalaibleNumber($stock['stock_id'], $stock['quantity']);
        }

        //transform value in tinyint (bool) (to fit into status table)
        $commande['isPick'] === 'false' ? $commande['isPick'] = 0 : $commande['isPick'] = 1;
        $commande['isPrepared'] === 'false' ?  $commande['isPrepared'] = 0 : $commande['isPrepared'] = 1;

        // insert command status
        $commandStatusManager = new CommandStatusManager();
        $commandStatusManager->insertStatus($commande);
    }


    /*
     * edit a command
    */
    public function editCommand($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commandManager = new CommandManager();
            $stockManager = new StockManager();
            $statusController = new CommandStatusController();

            $stock = $stockManager->selectAll();
            $commande = $_POST;
            $commande['totalAmount'] = 0;
            $commande['command_id'] = $id;

            //if pick date is modified
            if ($_POST['newDatePick'] <> '' && $_POST['newTimePick'] <> '') {
                $newDate = $commande['newDatePick'] . ' ' . $commande['newTimePick'];
                $commandManager->editDatePicksById($commande['command_id'], $newDate);
            }

            if (isset($_POST['isPick']) && isset($_POST['isPrepared'])) {
                $statusController->editStatus($commande);
            }

            //suppr pour eviter des problemess avec l'update car plusieurs meme id dans commandDetails
            //on supprimer tout le stock
            $commandManager->deleteDetails($id);


            //on réinsère le nouveau stock
            foreach ($commande['stock_id'] as $id => $quantities) {
                foreach ($quantities as $quantity) {
                    if (!empty($quantity) && (int) $quantity > 0) {
                        $commande['stock_id'] = (int)$id;
                        $commande['quantity'] = (int)$quantity;
                        //Insert new stock id
                        $commandManager->insertCommandDetails($commande);
                    }
                }
            }

            $details = $commandManager->getDetails($commande['command_id']);
            foreach ($stock as $flower) {
                foreach ($details as $detail) {
                    if ($flower['id'] === $detail['stock_id']) {
                        $commande['totalAmount'] += $flower['price'] * $detail['quantity'];
                        $commandManager->editCommand($commande);
                    }
                }
            }
            header('Location: /CommandStatus/showActiveCommand');
        }
    }

    /*
     * edit a command details : date pick
     */
    public function editDatePickById($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //format date pick to fit in base
            $newDate = $_POST['datePick'] . ' ' . $_POST['timePick'];

            $commandManager = new CommandManager();
            $commandManager->editDatePicksById($id, $newDate);
            header("Location: /Command/showAll");
        }
    }
}
