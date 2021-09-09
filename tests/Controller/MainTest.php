<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainTest extends WebTestCase
{
    public function testLogin(): void
    {
        // On simule l'accès à une la page /login
        // via un navigateur intégré
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Est ce que la page répond correctement
        $this->assertResponseIsSuccessful();

        // On vérifie également que la page de login possède bien
        // une balise h1 avec le contenu "Please sign in"
        // assertSelectorTextContains fait deux tests :
        // - Il vérifie la présence d'un h1 (1)
        // - il vérifie la présence du texte (2)
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }


    public function testRegister(): void
    {
        // On simule l'accès à une la page /login
        // via un navigateur intégré
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        // Est ce que la page répond correctement
        $this->assertResponseIsSuccessful();

        // On vérifie également que la page de login possède bien
        // une balise h1 avec le contenu "Please sign in"
        // assertSelectorTextContains fait deux tests :
        // - Il vérifie la présence d'un h1 (1)
        // - il vérifie la présence du texte (2)
        $this->assertSelectorTextContains('h1', 'Register');
    }
}
