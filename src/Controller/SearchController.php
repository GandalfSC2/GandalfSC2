<?php

namespace App\Controller;

use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search", name="search_")
 */
class SearchController extends AbstractController
{
    /**
     * URL : /search/
     * Route : search_index
     * 
     * 
     * Affiche les résultats d'une recherche
     * 
     * @Route("/", name="index")
     */
    public function index(Request $request, TvShowRepository $tvShowRepository): Response
    {
        // 1) On récupère le mot-clé saisi dans le formulaire de recherche
        $query = $request->query->get('search');

        // 2) On récupère toutes les séries qui contiennent ce mot-clé
        $results = $tvShowRepository->searchTvShowByTitleDQL($query);

        // 3) On affiche ensuite le résultat depuis la page /search
        return $this->render('search/index.html.twig', [
            'results' => $results,
            'query' => $query
        ]);
    }
}
