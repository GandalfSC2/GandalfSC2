<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TvShowTest extends WebTestCase
{
    /**
     * Test de l'accès à la page tvshow en mode non connecté.
     * 
     * Si l'on est pas connecté, 
     * on censé être redirigé vers la page de login
     *
     * @return void
     */
    public function testTvShowListPublic(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/backoffice/tvshow');

        // La page /backoffice/tvshow n'étant accessible qu'aux personnes
        // connectées, on est censé être redirigé vers la page de login
        $this->assertResponseRedirects();
    }

    /**
     * Test de l'accès à une page du backofffice en tant que simple
     * utilisateur (ROLE_USER)
     *
     * @return void
     */
    public function testTvShowRoleUser()
    {
        $client = static::createClient();

        // Avant de tester l'accès à la page on va d'abord se connecter
        // en tant que demo2@oclock.io
        $userRepository = static::getContainer()->get(UserRepository::class);

        // On récupère l'utilisateur alice qui a un role ROLE_USER
        $testUser = $userRepository->findOneByEmail('alice@oclock.io');

        // On simule une authentification
        // Simulation de la saisie d'un login + mot de passe
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/backoffice/tvshow');

        // On vérifie que lorsque Alice tente d'accèder à la page /backoffice/tvshow
        // le serveur retourne bien un code d'erreur 403 - Non autorisé
        // @copyright 2021 Morgane : $this->assertResponseStatusCodeSame(403);
        // Toutes les assertions de type response : https://symfony.com/doc/current/testing.html#response-assertions
        $this->assertEquals(403, $client->getResponse()->getStatusCode());


        // On récupère l'utilisateur alice qui a un role ROLE_USER
        // $testUser = $userRepository->findOneByEmail('charles@oclock.io');

        // // On simule une authentification
        // // Simulation de la saisie d'un login + mot de passe
        // $client->loginUser($testUser);

        // $crawler = $client->request('GET', '/backoffice/tvshow/');

        // // On vérifie que l'utilisateur admin a bien accès à la page
        // $this->assertResponseIsSuccessful();
    }

    /**
     * Vérification de l'accès au backoffice par des utilisateurs
     * ayant un role ADMIN
     * 
     * @return void
     */
    public function tvShowRoleAdmin()
    {
        $client = static::createClient();

        // Avant de tester l'accès à la page on va d'abord se connecter
        // en tant que demo2@oclock.io
        $userRepository = static::getContainer()->get(UserRepository::class);

        // On récupère l'utilisateur alice qui a un role ROLE_USER
        $testUser = $userRepository->findOneByEmail('charles@oclock.io');

        // On simule une authentification
        // Simulation de la saisie d'un login + mot de passe
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/backoffice/tvshow/');

        // On vérifie que l'utilisateur admin a bien accès à la page
        $this->assertResponseIsSuccessful();
    }
}
