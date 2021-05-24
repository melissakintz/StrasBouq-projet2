<?php

namespace App\Controller;

use App\Model\StockManager;

class StockController extends AbstractController
{
    public function index(): string
    {
        if ($_SESSION['admin'] === false) {
            header('Location: /Home/accessdenied');
        }
        $stockManager = new StockManager();
        $stocks = $stockManager->selectAll('name');

        return $this->twig->render('Stock/index.html.twig', ['stocks' => $stocks]);
    }

    public function show(int $id): string
    {
        if ($_SESSION['admin'] === false) {
            header('Location: /Home/accessdenied');
        }
        $stockManager = new StockManager();
        $stock = $stockManager->selectOneByID($id);

        return $this->twig->render('Stock/show.html.twig', ['stock' => $stock]);
    }

    public function edit(int $id): string
    {
        if ($_SESSION['admin'] === false) {
            header('Location: /Home/accessdenied');
        }
        $stockManager = new StockManager();
        $stock = $stockManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stock = array_map('trim', $_POST);
            $stock['image'] = $this->gererImage($_FILES);
            $stockManager->update($stock);
            header('Location: /stock/show/' . $id);
        }

        return $this->twig->render('Stock/edit.html.twig', [
        'stock' => $stock,
        ]);
    }

    public function add(): string
    {
        if ($_SESSION['admin'] === false) {
            header('Location: /Home/accessdenied');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stock = array_map('trim', $_POST);
            $stock['image'] = $this->gererImage($_FILES);
            $stockManager = new StockManager();
            $id = $stockManager->insert($stock);
            header('Location:/stock/show/' . $id);
        }

        return $this->twig->render('Stock/add.html.twig');
    }

    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stockManager = new StockManager();
            $stockManager->delete($id);
            header('Location:/stock/index');
        }
    }

    /*
     * decrease available number in function of the command
     */
    public function decreaseAvalaibleNumber(int $id, int $quantity): void
    {
        //get avalaible number
        $stockManager = new StockManager();
        $avalaibleNumber = $stockManager->selectAvalaibleNumberById($id);

        //get the new avalaible number
        $quantity = $avalaibleNumber['avalaibleNumber'] - $quantity;
        $stockManager->decreaseAvalaibleNumber($id, $quantity);
    }

    public function gererImage(array $files)
    {
        $fileTmpName = $files['fleur']['tmp_name'];
        $fileNameNew = uniqid('filename -', true);

        $baseName = basename($files['fleur']['name']);
        $fileDestination = "./assets/images/" . $fileNameNew . $baseName;
        move_uploaded_file($fileTmpName, $fileDestination);
        $extension = pathinfo($files['fleur']['name'], PATHINFO_EXTENSION);
        $extensionsOk = ['jpg', 'jpeg', 'png', 'webp'];
        $maxFileSize = 2000000;

        if ((!in_array($extension, $extensionsOk))) {
            $errors = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png ou webp !';
            echo $errors;
        }
        if (file_exists($files['fleur']['tmp_name']) && filesize($files['fleur']['tmp_name']) > $maxFileSize) {
            $errors = "Votre fichier doit faire moins de 2M !";
            echo $errors;
        }
        return $fileNameNew . $baseName;
    }
}
