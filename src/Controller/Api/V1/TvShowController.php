<?php

namespace App\Controller\Api\V1;

use App\Entity\TvShow;
use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/tvshows", name="api_v1_tvshow_")
 */
class TvShowController extends AbstractController
{
    /**
     * URL : /api/v1/tvshows/
     * Route : api_v1_tvshow_index
     * 
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(TvShowRepository $tvShowRepository): Response
    {
        // On récupère les séries stockées en BDD
        $tvShows = $tvShowRepository->findAll();

        // On retourne la liste au format JSON
        // Pour résoudre le bug : Reference circular
        return $this->json($tvShows, 200, [], [
            // Cette entrée indique au Serialiser de transformer les objets
            // en JSON, en allant chercher uniquement les propriétés
            // taggées avec le nom tvshow_list
            'groups' => 'tvshow_list'
        ]);
    }

    /**
     * Retourne les informations d'une série en fonction de son ID
     * 
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function show(int $id, TvShowRepository $tvShowRepository)
    {
        // On récupère une série en fonction de son id
        $tvShow = $tvShowRepository->find($id);

        // Si la série n'existe pas, on retourne une erreur 404
        if (!$tvShow) {
            return $this->json([
                'error' => 'La série ' . $id . ' n\'existe pas'
            ], 404);
        }

        // On retourne le résultat au format JSON
        return $this->json($tvShow, 200, [], [
            'groups' => 'tvshow_detail'
        ]);
    }

    /**
     * Permet la création d'une nouvelle série
     * 
     * URL : /api/v1/tvshows/
     * 
     * @Route("/", name="add", methods={"POST"})
     *
     * @return void
     */
    public function add(Request $request, SerializerInterface $serialiser, ValidatorInterface $validator)
    {
        // 1) On récupère le JSON
        $jsonData = $request->getContent();

        // 2) On transforme le json en objet : désérialisation
        // - On indique les données à transformer (désérialiser)
        // - On indique le format d'arrivé après conversion (objet de type TvShow)
        // - On indique le format de départ : on veut passer de json vers un objet TvShow
        $tvShow = $serialiser->deserialize($jsonData, TvShow::class, 'json');

        // On valide les données stockées dans l'objet $tvShow en basant
        // sur les crtières de l'annotation @Assert de l'entité (cf. src/Entity/TvShow.php)
        // Doc symfony : https://symfony.com/doc/current/validation.html#using-the-validator-service
        $errors = $validator->validate($tvShow);

        // Si la tableau d'erreurs n'est pas vide (au moins 1 erreur)
        // count permet de compte le nombre d'éléments d'un tableau
        // count([1, 2, 3]) ==> 3
        if (count($errors) > 0) {
            // Code 400 : bad request , les données reçues ne sont
            // pas conformes
            return $this->json($errors, 400);
        }

        // Pour sauvegarder, on appelle le manager
        $em = $this->getDoctrine()->getManager();
        $em->persist($tvShow);
        $em->flush();

        // On retourne une réponse en indiquant que la ressource
        // a bien été créée (code http 201)
        return $this->json($tvShow, 201);
    }

    /**
     * Mise à jour d'une série en fonction de son Identifiant
     * 
     * @Route("/{id}", name="update", methods={"PUT", "PATCH"})
     *
     * @return void
     */
    public function update(int $id, TvShowRepository $tvShowRepository, Request $request, SerializerInterface $serialiser)
    {
        // On récupère les données reçues au format JSON
        $jsonData = $request->getContent();

        // On récupère la série dont l'ID est $id
        $tvShow = $tvShowRepository->find($id);

        if (!$tvShow) {
            // Si la série à mettre à jour n'existe pas
            // on retourne un message d'erreur (400::bad request ou 404:: not found)
            return $this->json(
                [
                    'errors' => [
                        'message' => 'La série ' . $id . ' n\'existe pas'
                    ]
                ],
                404
            );
        }

        // On fusionne les données de la série avec les données
        // issue de l'application Front (insomnia, react, ...)
        // Deserializing in an Existing Object : https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        // On demande au serializer de transformer les données JSON($jsonData)
        // en objet de classe TvShow, tout en fusionnant ces données avec
        // l'objet existant $tvShow

        $serialiser->deserialize($jsonData, TvShow::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $tvShow]);

        // On appelle le manager pour effectuer la mise à jour en BDD
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json([
            'message' => 'La série ' . $tvShow->getTitle() . ' a bien été mise à jour'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function delete(int $id, TvShowRepository $tvShowRepository)
    {
        $tvShow = $tvShowRepository->find($id);

        if (!$tvShow) {
            // La série n'existe pas
            return $this->json(
                [
                    'errors' => ['message' => 'La série ' . $id . ' n\'existe pas']
                ],
                404
            );
        }

        // On appelle le manager pour gérer la suppresion de la série
        $em = $this->getDoctrine()->getManager();
        $em->remove($tvShow);
        $em->flush();

        return $this->json([
            'message' => 'La série ' . $id . ' a bien été supprimée'
        ]);
    }
}
