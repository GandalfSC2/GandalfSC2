<?php

namespace App\Command;

use App\Repository\TvShowRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

class TvshowSluggerCommand extends Command
{
    // php bin/console tvshow:slugger
    protected static $defaultName = 'tvshow:slugger';

    // php bin/console tvshow:slugger --help
    protected static $defaultDescription = 'Permet de mettre à jour le slug de toutes les séries';

    private $tvShowRepository;
    private $slugger;
    private $manager;
    public function __construct(TvShowRepository $tvShowRepository, SluggerInterface $slugger, EntityManagerInterface $manager)
    {
        $this->tvShowRepository = $tvShowRepository;
        $this->slugger = $slugger;
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // php bin/console tvshow:slugger 2
            ->addArgument('id', InputArgument::OPTIONAL, 'Identifiant de la série à modifier')

            // php bin/console tvshow:slugger 2 --updatedAt
            ->addOption('updatedAt', null, InputOption::VALUE_NONE, 'Option de mise à jour de la propriété updatedAt');
    }

    /**
     * C'est dans cette méthode qu'on va coder toute la logique
     * métier de notre commande
     * 
     * On va générer les slugs de toutes nos séries O'flix
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tvShowId = $input->getArgument('id');
        $updatedAtOption = $input->getOption('updatedAt');

        // Bonus : Arguments et Options
        if ($tvShowId) {
            // On récupère la série dont l'id est $tvShowId
            $tvShow = $this->tvShowRepository->find($tvShowId);
            if ($tvShow) {
                $this->createSlug($tvShow, $io, $updatedAtOption);
            } else {
                $io->error('La série dont l\'id est ' . $tvShowId . ' n\'est pas définie.');
                return Command::INVALID;
            }
        } else {
            // Etape 1 : On récupère toutes les séries de la BDD
            $tvShowList = $this->tvShowRepository->findAll();
            // Etape 2 : Pour chaque série
            foreach ($tvShowList as $tvShow) {
                $this->createSlug($tvShow, $io, $updatedAtOption);
            }
        }

        // Etape 4 : On sauvegarde les séries en BDD
        $this->manager->flush();

        $io->success('Toutes les séries ont bien été mises à jour.');

        // On fait savoir à Symfony que tout s'est bien passsé : 
        // c'est Toutou beignet
        return Command::SUCCESS;
    }

    private function createSlug($tvShow, $io, $updatedAt)
    {
        // TODO : Rajouter un test pour éviter d'avoir des slugs
        // identiques
        // findOneBy(['slug' => $slug])
        // $slug - uniqueid
        // Etape 3 : On met à jour la propriété slug de la série
        $title = $tvShow->getTitle(); // Série "Breaking Bad"
        $slug = $this->slugger->slug($title); // Breaking-Bad
        $slug = strtolower($slug); // breaking-bad
        $io->text('Mise à jour de la série ' . $title . ' en cours');

        $tvShow->setSlug($slug);

        if ($updatedAt) {
            // Si l'option updatedAt est définie dans la commande
            // alors on met à jour la propriété updatedAt
            $tvShow->setUpdatedAt(new DateTimeImmutable());
        }
    }
}
