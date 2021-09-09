<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service permettant de récupérer des informations d'une série
 * à partir depuis le site https://omdbapi.com/
 */
class OmdbApi
{
    private $client;
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Récupère les informations d'une série via l'API OmdbAPI
     * en fonction du titre
     *
     * @param string $title
     * @return Array
     */
    public function fetch(string $title): array
    {
        // On va passer en mode "client" pour interroger l'API
        // grâce à la classe HTTP Client

        $response = $this->client->request(
            'GET',
            // 'https://omdbapi.com/?apiKey=83bfb8c6&t=Dark'
            'https://omdbapi.com/?apiKey=83bfb8c6&t=' . $title
        );

        // On retourne les informations de la série sous la forme
        // d'un tableau associatif
        return $response->toArray();
    }
}
