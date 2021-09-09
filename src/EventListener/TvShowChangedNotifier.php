<?php

namespace App\EventListener;

use App\Entity\TvShow;
use App\Service\OmdbApi;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class TvShowChangedNotifier
{
    private $slugger;
    private $omdbApi;
    public function __construct(SluggerInterface $slugger, OmdbApi $omdbApi)
    {
        $this->slugger = $slugger;
        $this->omdbApi = $omdbApi;
    }

    /**
     * Création d'un slug (et récupération d'infos d'OmdbAPI si non présents) à la création ou la mise à jour d'une série
     * Voir le fichier config/services.yaml (section : App\EventListener\TvShowChangedNotifier:)
     *
     * @param TvShow $tvShow
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function updateTvShowData(TvShow $tvShow, LifecycleEventArgs $event)
    {
        // Création d'un slug
        $title = strtolower($tvShow->getTitle());
        $tvShow->setSlug($this->slugger->slug($title));

        // Ajout d'un Poster si besoin
        if (!$tvShow->getImage()) {
            $tvShowData = $this->omdbApi->fetch($title);
            if ($tvShowData && isset($tvShowData['Poster'])) {
                $tvShow->setImage($tvShowData['Poster']);
            }
        }
    }
}
