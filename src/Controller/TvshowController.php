<?php

namespace App\Controller;

use App\Repository\TvshowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TvshowController extends AbstractController
{
    /**
     * @Route("/tvshow", name="tvshow")
     */
    public function index(TvshowRepository $tvshowRepository): Response
    {
        $tvShowAll = $tvshowRepository->findAll();

        return $this->render('tvshow/index.html.twig', [
            'controller_name' => $tvShowAll,
        ]);
    }
}
