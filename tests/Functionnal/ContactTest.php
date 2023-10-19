<?php

namespace App\Tests\Functionnal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', "Formulaire de contact d'un administrateur");

        // Get the form and set the info
        $submit = $crawler->selectButton('Valider');
        $form = $submit->form();

        $form["contact[name]"] = "Michmich";
        $form["contact[email"] = "michmich@gmail.com";
        $form["contact[title]"] = "Ajout de jeu";
        $form["contact[content]"] = "Bonjour, il faudrait ajouter le jeu X. Merci.";

        // Submit it
        $client->submit($form);

        // Check HTTP status
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Check if the email is sent
        $this->assertEmailCount(1);

        $client->followRedirect();

        // Check if the success message is display
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            'Votre demande a été envoyée avec succès !'
        );
    }
}
