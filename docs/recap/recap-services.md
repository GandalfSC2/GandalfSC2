# Les services

Les services sont des classes que l'ont peut poser n'importe où dans notre projet et qui servent d'utilitaires. Elles ont un role précis.
Ca sert à limiter le code dans les controllers et éviter au maximum les copier-coller.

## a) Créer un service

- On place généralement les services dans **src/Service** (Dossier à créer)
- On crée un fichier par service
- Dans ce fichier on crée une classe classique qui effectue la tâche voulue

On peut ensuite importer nos services:

- Dans les controllers : En injection de dépendance directement dans la méthode du controller ou depuis le constructeur
- Dans tout autre classe : dans le constructeur

```php

// src/Service/TestService.php
<?php

namespace App\Service;


class TestService
{
    public function sayHello()
    {
        return 'Hello world';
    }
}


// ..

// src/Controller/HomeController.php

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TvShowRepository $tvShowRepository, TestService $test): Response
    {

        $this->addFlash('success', $test->sayHello());

        //...
    }
    
 ..
 }
```

## 📑Pour aller plus loin

- [Service Container](https://symfony.com/doc/current/service_container.html)
