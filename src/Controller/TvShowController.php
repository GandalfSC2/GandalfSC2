<?php

namespace App\Controller;

use App\Entity\TvShow;
use App\Repository\TvShowRepository;
use App\Service\OmdbApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/tvshow", name="tvshow_", requirements={"id":"\d+"})
 * 
 * @IsGranted("ROLE_USER")
 */
class TvShowController extends AbstractController
{
    /**
     * Page affichant la liste des séries
     * 
     * URL : /tvshow/
     * Nom de la route : tvshow_index
     * 
     * @Route("/", name="index")
     */
    public function index(TvShowRepository $tvShowRepository): Response
    {
        // Pour afficher la liste des séries, il faut être connecté
        // $this->denyAccessUnlessGranted('ROLE_USER');

        // On récupère toutes les séries pour les afficher 
        // depuis /tvshow/
        $tvShowList = $tvShowRepository->findAll();

        return $this->render('tv_show/index.html.twig', [
            'tvShowList' => $tvShowList,
        ]);
    }

    /**
     * Affiche les détails d'une série en fonction
     * de son Identifiant
     * 
     * URL : /tvshow/{id}
     * Nom de la route : tvshow_show
     * @Route("/{id}", name="show")
     * 
     * URL : /tvshow/{slug}
     * Nom de la route : tvshow_slug
     * @Route("/{slug}", name="slug")
     *
     * @return void
     */
    public function show(TvShow $tvShow, OmdbApi $omdbApi)
    {
        // On autorise l'accès aux détails d'une série uniquement
        // aux personnes connectées
        // $this->denyAccessUnlessGranted('ROLE_USER');

        // On récupère la série dont l'ID est égal à $id
        // $tvShow = $tvShowRepository->find($id);

        // TEST DU SERVICE OMDBAPI
        // $tvShowDataArray = $omdbApi->fetch($tvShow->getTitle());

        // Si la série n'existe pas, alors on affiche une 404
        if (!$tvShow) {
            throw $this->createNotFoundException("La série $id n'existe pas");
        }

        // on transmet les informations de la série à la vue
        // templates/tv_show/show.html.twig
        return $this->render('tv_show/show.html.twig', [
            'tvShow' => $tvShow
        ]);
    }
}
