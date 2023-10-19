<?php

namespace App\Tests\Functionnal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $signinLink = $crawler->selectLink('Inscription');
        $this->assertEquals(2, count($signinLink));

        $loginLink = $crawler->selectLink('Connexion');
        $this->assertEquals(1, count($loginLink));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur Games & Friends');
    }
}
