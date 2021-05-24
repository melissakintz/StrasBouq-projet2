<?php

namespace App\Controller;

use App\Model\CommandStatusManager;

class CommandStatusController extends AbstractController
{
    /* Made by Mélissa Kintz */

    /**
     * Show command status by id
     */
    public function showById(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commandStatusManager = new CommandStatusManager();
            $status = $commandStatusManager->selectOneById($id);

            //transform value for more comprehension for the view
            if (!empty($status)) {
                if ($status['ispick'] === '0') {
                    $status['ispick'] = 'Non';
                } elseif ($status['ispick'] === '1') {
                    $status['ispick'] = 'Oui';
                }

                if ($status['isprepared'] === '0') {
                    $status['isprepared'] = 'Non';
                } elseif ($status['isprepared'] === '1') {
                    $status['isprepared'] = 'Oui';
                }
            }

            return $this->twig->render("Commande/addCommand.html.twig", ['status' => $status]);
        }
    }

    /*
     * edit status
     */
    public function editStatus($commande)
    {
        $ispick = $commande['isPick'];
        $isprepared = $commande['isPrepared'];

            //transform value to fit in the table
        if ($ispick === 'false') {
            $ispick = 0;
        } elseif ($ispick === 'true') {
            $ispick = 1;
        }

        if ($isprepared === 'false') {
            $isprepared = 0;
        } elseif ($isprepared === 'true') {
            $isprepared = 1;
        }

        $commandStatusManager = new CommandStatusManager();
        $commandStatusManager->editStatus($commande['command_id'], $ispick, $isprepared);

        header("Location: /Command/showAll");
    }

    /* Made by Jerome Bach */

    /*
    * display command already picked
    */
    public function showArchiveCommand()
    {
        $commandStatusManager = new CommandStatusManager();
        $archiveCommand = $commandStatusManager->archiveCommand('dateOrder');


        return $this->twig->render("Commande/archive.html.twig", ['archivecommand' => $archiveCommand]);
    }

    /*
     * display command not picked
     */
    public function showActiveCommand()
    {
        $commandStatusManager = new CommandStatusManager();
        $activeCommand = $commandStatusManager->activeCommand('datePick');

        return $this->twig->render("Commande/command.html.twig", ['activecommand' => $activeCommand]);
    }
}
