# API

## Intro
Une API est une interface de programmation permettant d'échanger des informations internes avec d'autres applications (Front React, Appli mobile, Smartwatch, ...ou l'appli elle-même).

On va respecter un standar nommé REST, pour créer une API dite  :

- [Doc sur les bonnes pratiques](https://medium.com/@mwaysolutions/10-best-practices-for-better-restful-api-cbe81b06f291)

- Utiliser des noms et pas des verbes dans les routes, par exemple, on met un /shows pour récupérer toutes les séries. Et /shows/id pour récupérer une série.

- En GET, on ne modifie rien sur les entités, on s'en sert juste pour récupérer de l'information. Pour "modifier" quelque chose, on utilise POST, PUT, PATCH, DELETE. On respecte le protocole HTTP.
  - GET : lire une ressource
  - POST : créer une ressource
  - PUT  : mettre à jour toutes les propriétés d'une ressource
  - PATCH : met à jour partiellement une ressource
  - DELETE : supprime une ressource

- Utiliser les entêtes HTTP qui correspondent exactement au format retourné (Content-type et Accept)

- Utiliser les bons codes HTTP : 200 pour les succès (200 OK, 201 Created, 202 Accepted, 204 No content (Quand on supprime une info avec succès) ), 300 pour les redirections, 400 pour les erreurs du client web (400 Bad request, 404 Unhaorized (Il faut se connecter pour accéder à la ressource), 403 Forbidden (Le user n'a pas accès au contenu), 404 Not Found, 405 Method not allowed ), 500 pour les erreurs du serveur (500 Internal servor error)
- On versionne son API
- ...

## Routes de notre applications

TvShow :

- Liste des séries `/api/v1/tvshows` - GET
- Une série par ID `/api/v1/tvshows/{id}` - GET
- Créer une série `/api/v1/tvshows` - POST
- Mise à jour totale d'une série `/api/v1/tvshows/{id}` - PUT
- Mise à jour partielle d'une série (ex : uniquement le title) `/api/v1/tvshows/{id}` - PATCH
- Supprimer une série : `/api/v1/tvshows/{id}` - DELETE

Seasons de la série dont l'ID est égal à id
- Liste des séries `/api/v1/tvshows/id/seasons`


Categories

- Liste des catégories `/api/v1/categories`
- Une catégorie par ID `/api/v1/categories/id`

## Création de notre API

Pour créér une API permettant la gestion des Tv Shows, on va créer un controller avec le maker :
- `php bin/console make:controller --no-template`
```bash
 Choose a name for your controller class (e.g. GrumpyGnomeController):
 > Api\V1\TvShow

 created: src/Controller/Api/V1/TvShowController.php
           
 Success!           

 Next: Open your new controller class and add some pages!
```


Pour résoudre le bug suivant :
```
A circular reference has been detected when serializing the object of class "App\Entity\TvShow" (configured limit: 1).
```

On va aider le composant serialiser à transformer un tableau d'objets en JSON, en lui les indiquant les propriétés à appeler dans toutes les entités qui nous intéressent. 

```php
// src/Entity/TvShow.php

use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=TvShowRepository::class)
 */
class TvShow
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups({"tvshow_list", "tvshow_detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tvshow_list", "tvshow_detail"})
     */
    private $title;

    // ...
}
```

Ensuite, dans le controleur, on appelle la méthode `json(...)` en indiquant le bon groupe dans l'attribut `context`

```php
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
```

## Sécurité

Pour sécuriser notre API, nous allons utiliser le bundle Lexik JWT, qui nous permettra de générer un token JWT (Json Web Token). Ce dernier servira de "badge" à nos applications Front qui interagiront avec notre API.

Toutes les étapes d'installation et de configuration du bundle se trouve [ici](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.md#getting-started).

Le bundle utilise une "passphrase" pour générer le token. Celle-ci est par défaut présente dans le fichier `.env`. Pour ne pas divulguer cette information en la pushant, il est recommandée de l'écrire dans le fichier `.env.local` 

```bash
JWT_PASSPHRASE=607fa907c99111a7a5cce5dd4a7cd616
```

- :book: [Lexik JWT](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.md#getting-started)

## Gestion du CORS

Pour des raisons, tous les navigateurs du monde entier ont le devoir ne pas autoriser de requetes Ajax entre des serveurs ayant des origines différentes :

- Depuis `localhost:8000` je ne peux interroger en Ajax le site `localhost:8080`, à moins que celui ne m'y autorise.

Pour autoriser certaines origin à communiquer avec notre API, on va installer le [bundle Nelmio](https://github.com/nelmio/NelmioCorsBundle) :
- `composer req cors`