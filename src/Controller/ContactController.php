<?php

namespace App\Controller;

class ContactController extends AbstractController
{
    /* Made by Mélissa Kintz */

    /*
     * send mail (contact form)
     */
    public function contactUs()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $destinataire = "example@mail.com";
            $objet = "Retour client site";
            $client = "Nom: " . $_POST['lastname'] . ' Prénom : ' . $_POST['firstname'];
            $message = $_POST['message'] . '<br/>' . $client;

            $headers = 'From: ' . $_POST['email'] . "\n";
            $headers .= 'Reply-To: adresse_de_reponse@fai.fr' . "\n";
            $headers .= 'Content-Type: text/plain; charset="iso-8859-1"' . "\n";
            $headers .= 'Content-Transfer-Encoding: 8bit';

            //envoi du mail
            $retour = mail($destinataire, $objet, $message, $headers);
            $retour ? $envoi = 'Votre message à bien était envoyé' : $envoi = "Erreur lors de l'envoi";
            return $this->twig->render('/Home/contact.html.twig', ['envoi' => $envoi]);
        }
    }
}
