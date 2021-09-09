<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    /**
     * Test de la page d'accueil en monde non connecté (public)
     *
     * @return void
     */
    public function testHomePagePublic(): void
    {
        // On va se mettre dans la peau d'un navigateur
        // Et tenter d'accéder à la page d'accueil ("/")
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // On vérifie ensuite si la page existe bien.
        // Si c'est OK, alors la page est potentiellement fonctionnelle
        $this->assertResponseIsSuccessful();

        // On vérifie s'il existe une balise h1 avec le contenu : 
        // "Séries TV et bien plus en illimité."
        $this->assertSelectorTextContains('h1.fw-light', 'Séries TV et bien plus en illimité.');
    }

    /**
     * Test de la page en mode connecté
     *
     * @return void
     */
    public function testHomePageConnected()
    {
        // Logging in Users (Authentication) :https://symfony.com/doc/current/testing.html#logging-in-users-authentication
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // $testUser = $userRepository->findOneByFirstName('Charles');
        // findOneBy(["email" => "charles@oclock.io"])
        $testUser = $userRepository->findOneByEmail('charles@oclock.io');

        // On simule une authentification
        // Simulation de la saisie d'un login + mot de passe
        $client->loginUser($testUser);

        // On teste l'accès à la page d'accueil en tant qu'utilisateur
        // connecté
        $crawler = $client->request('GET', '/');

        // On vérifie que la page est dispo
        $this->assertResponseIsSuccessful();

        // On vérifie que dans la page d'accueil on retrouve bien
        // le texte suivante : Bienvenue leprenom
        $this->assertSelectorTextContains('main div.col-lg-6 p:nth-child(3)', 'Bienvenue ' . $testUser->getFirstname());
    }
}
