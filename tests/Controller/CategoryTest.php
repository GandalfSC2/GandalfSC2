<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    /**
     * Test de l'ajout d'une catégorie dans le backoffice
     *
     * @return void
     */
    public function testCategoryAdd(): void
    {
        // Doc symfony : https://symfony.com/doc/current/testing.html#submitting-forms
        $client = static::createClient();

        // Avant de tester l'accès à la page on va d'abord se connecter
        // en tant que demo2@oclock.io
        $userRepository = static::getContainer()->get(UserRepository::class);

        // On récupère l'utilisateur charles qui a un role ROLE_ADMIN
        $testUser = $userRepository->findOneByEmail('charles@oclock.io');

        // On simule une authentification
        // Simulation de la saisie d'un login + mot de passe
        $client->loginUser($testUser);

        // On accède au formulaire d'ajout d'une catégorie depuis le backoffice
        $crawler = $client->request('GET', '/backoffice/category/add');

        // On simule le submit du formulaire
        // la méthode submitForm va chercher le bouton submit du formulaire
        // contenant le texte "Valider"
        $crawler = $client->submitForm('Valider', [
            'category[name]' => 'Drame',
        ]);

        // On vérifie qu'au submit, on est redirigé vers la page 
        // /backoffice/category
        $this->assertResponseRedirects('/backoffice/category/');
    }
}
