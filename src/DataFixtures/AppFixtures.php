<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Character;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\TvShow;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Character($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\TvShow($faker));

        // Création de Personnages
        print "Création de Personnages";
        $characterObjectList = [];
        for ($index = 0; $index < 10; $index++) {
            $gender = mt_rand(0, 1) ? 'male' : 'female';
            $fullNameArray = explode(" ", $faker->character($gender));

            // On créé un personnage vide
            $character = new Character();
            $character->setFirstname($fullNameArray[0]);
            $character->setLastname($fullNameArray[1] ?? ' Doe' . $index);
            $character->setGender($gender == 'male' ? 'Homme' : 'Femme');

            // On met le personnage en liste d'attente
            // pour une sauvegarde au moment du "flush"
            $characterObjectList[] = $character;
            $manager->persist($character);

            print 'Personnage : ' . $character->getFirstname() . ' : OK';
        }

        $categoryNamesList = [
            'Action',
            'Animation',
            'Aventure',
            'Comédie',
            'Dessin animé',
            'Documentaire',
            'Drame',
            'Espionnage',
            'Famille',
            'Fantastique',
            'Historique',
            'Policier',
            'Romance',
            'Science-fiction',
            'Thriller',
            'Western'
        ];
        $categoryObjectList = [];
        print 'Création des catégories';
        foreach ($categoryNamesList as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $categoryObjectList[] = $category;
            $manager->persist($category);
        }

        print 'Séries Tv en cours...';
        for ($i = 0; $i < 20; $i++) {
            // On créé une série vide
            $tvShow = new TvShow();

            // On ajoute des informations
            $tvShow->setTitle($faker->tvShow);
            $tvShow->setSynopsis($faker->overview);
            $tvShow->setNbLikes(150000);

            print 'Série ' . $tvShow->getTitle() . 'en cours...';

            // On créé de nouvelles saisons
            for ($j = 0; $j < 2; $j++) {
                $seasonObject = new Season();
                $seasonObject->setSeasonNumber($j);

                // On créé entre 1 et 5 épisodes
                for ($k = 0; $k < mt_rand(0, 4); $k++) {
                    $episode = new Episode();
                    $episode->setEpisodeNumber($k);
                    $episode->setTitle('Episode #' . $k);
                    $seasonObject->addEpisode($episode);
                    $manager->persist($episode);
                }

                // On rajoute la saison aux objets à créer
                $manager->persist($seasonObject);

                // On associe la saison à la série
                $tvShow->addSeason($seasonObject);
            }


            // On associe la série à au plus 3 catégories
            for ($index = 0; $index < mt_rand(0, 2); $index++) {
                $tvShow->addCategory($categoryObjectList[$index]);
            }

            // On associe la série à 4 personnages maximum
            for ($index = 0; $index < mt_rand(0, 4); $index++) {
                $tvShow->addCharacter($characterObjectList[$index]);
            }

            $manager->persist($tvShow);
        }


        // création de 3 comptes utilisateurs
        $users = [
            ['email' => 'charles@oclock.io', 'mdp' => 'demo123', 'firstname' => 'Charles', 'lastname' => 'O\'clock'],
            ['email' => 'alice@oclock.io', 'mdp' => 'demo123', 'firstname' => 'Alice', 'lastname' => 'O\'clock'],
            ['email' => 'bob@oclock.io', 'mdp' => 'demo123', 'firstname' => 'Bob', 'lastname' => 'O\'clock']
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $userData['mdp']
                )
            );

            $manager->persist($user);
        }

        // On sauvegarde les séries/saisons/episodes/categories/users en BDD
        $manager->flush();
    }
}
