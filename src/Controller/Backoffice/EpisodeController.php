<?php

namespace App\Controller\Backoffice;

use App\Entity\Episode;
use App\Entity\Season;
use App\Form\EpisodeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/episode", name="backoffice_episode_")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/{id}", name="backoffice_episode_index")
     * 
     * Affiche tous les épisodes d'une saison (elle appartenant à une série)
     */
    public function index(): Response
    {
        return $this->render('backoffice/episode/index.html.twig', [
            'controller_name' => 'EpisodeController',
        ]);
    }


    /**
     * @Route("/{id}/add", name="add")
     * 
     * Permet la création d'un épisode 
     * que l'on associe ensuite à une saison
     *
     * @return Response
     */
    public function add(Season $season, Request $request)
    {
        $episode = new Episode();
        $form =  $this->createForm(EpisodeType::class, $episode);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $season->addEpisode($episode);

            $em = $this->getDoctrine()->getManager();
            $em->persist($episode);
            $em->flush();

            $this->addFlash('success', 'L\'episode ' . $episode->getTitle() . ' a bien été créé');

            return $this->redirectToRoute('backoffice_season_index', ['id' => $season->getTvShow()->getId()]);
        }

        return $this->render('backoffice/episode/add.html.twig', [
            'formView' => $form->createView(),
            'season' => $season
        ]);
    }
}
