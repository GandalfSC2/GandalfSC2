<?php

namespace App\Command;

use App\Repository\TvShowRepository;
use App\Service\OmdbApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TvshowPosterCommand extends Command
{
    // Nom de la commande à saisir dans le terminal
    // php bin/console tvshow:poster
    protected static $defaultName = 'tvshow:poster';

    // Permet d'afficher la description de la commande
    // lorsqu'on la lance avec l'option --help 
    // php bin/console tvshow:poster --help
    protected static $defaultDescription = 'Permet de mettre à jour les posters de toutes les séries à partir d\'une API';

    // Lorsque l'on souhaite appeler un Service dans une classe
    // autre qu'un controleur, on est obligé  de créer un constructeur
    // pour effectuer l'injection de ce service
    private $omdbApi;
    private $tvShowRepository;
    private $manager;
    public function __construct(OmdbApi $omdbApi, TvShowRepository $tvShowRepository, EntityManagerInterface $manager)
    {
        // On va pouvoir récupérer les séries
        $this->tvShowRepository = $tvShowRepository;

        // On va récupérer les posters pour chaque série
        $this->omdbApi = $omdbApi;

        // On va sauvegarder nos séries en BDD
        $this->manager = $manager;

        // On appelle le constructeur de la classe parent (Command)
        parent::__construct();
    }

    protected function configure(): void
    {   // Ici on met à jour uniquement la série dont l'ID est 2
        // et au passage on met à jour la colonne updatedAt
        // php bin/console tvshow:poster 2 --updatedAt
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');
        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }
        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // On va mettre la propriété `image` à partir de données issues
        // de OmdbAPI

        // Etape 1 : on récupère toutes les séries de notre BDD
        $tvShowList = $this->tvShowRepository->findAll();

        // Etape 2 : pour chaque série, on va interroger OmdbAPI
        // et récupérer le poster de la série
        foreach ($tvShowList as $tvShow) {
            $title = $tvShow->getTitle();
            $tvShowData = $this->omdbApi->fetch($title);
            if (isset($tvShowData['Poster'])) {
                $tvShow->setImage($tvShowData['Poster']);
            }

            // if ('bug') {
            //     return Command::FAILURE;
            // }

            $io->text('Mise à jour de la série ' . $title . ' en cours...');
        }

        // Etape 3 : on appelle le manager pour sauvegarder les séries en BDD
        $this->manager->flush();

        // $io->error('Erreur lors de la mise à jour des séries');
        $io->success('Mise de toutes les séries effectuée avec succès !');

        return Command::SUCCESS;
    }
}
