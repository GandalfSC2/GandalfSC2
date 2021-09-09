<?php

namespace App\Controller\Backoffice;

use App\Entity\Season;
use App\Entity\TvShow;
use App\Form\SeasonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/season", name="backoffice_season_")
 */
class SeasonController extends AbstractController
{
    /**
     * Affiche les saisons associées à une série dont l'ID est $id
     * 
     * @Route("/{id}", name="index")
     */
    public function index(TvShow $tvShow): Response
    {
        return $this->render('backoffice/season/index.html.twig', [
            'tvShow' => $tvShow,
        ]);
    }

    /**
     * 
     * URL : /backoffice/season/{id}/add
     * Route : backoffice_season_add
     * 
     * Permet l'ajout d'une saison à la série dont l'id est $id
     * 
     * @Route("/{id}/add", name="add")
     *
     * @return void
     */
    public function add(TvShow $tvShow, Request $request)
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO : gérer les doublons dans les saisons
            // TODO : tester l'ordre des numéros de saisons : 1 avant 2, ou 2 avant 1 autorisé ?

            // On associe la saison créée à la série $tvShow
            $tvShow->addSeason($season);

            // On sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($season);
            $em->flush();

            $this->addFlash('success', 'La saison numero ' . $season->getSeasonNumber() . ' a bien été associée à la série ' . $tvShow->getTitle());

            // On redirige vers la page listant toutes les saisons
            // d'une série
            return $this->redirectToRoute('backoffice_season_index', ['id' => $tvShow->getId()]);
        }

        return $this->render('backoffice/season/add.html.twig', [
            'formView' => $form->createView(),
            'tvShow' => $tvShow
        ]);
    }
}
