# Custom command

Les commandes personnalisées nous permettre d'effectuer des actions simples ou complexes (en cascade : création de plusieurs dossier, mise à jour en BDD de milliers de documents, ...) depuis la ligne de commande.

## Création d'une nouvelle commande 

- `php bin/console make:command`

```bash
php bin/console make:command

 Choose a command name (e.g. app:fierce-pizza):
 > oflix:assets

 created: src/Command/OflixAssetsCommand.php

           
  Success! 
           

 Next: open your new command class and customize it!
 Find the documentation at https://symfony.com/doc/current/console.html

```

Puisque le nom de ma commande est `oflix:assets`, je vais pouvoir la lancer en faisant :

- `php bin/console oflix:assets`

Il ne nous reste plus qu'à modifier le fichier `src/Command/OflixAssetsCommand.php`.

Dans cette classe, on peut éditer les propriétés suivantes :

- `$defaultName = 'oflix:assets'` : pour changer le nom de la commande
  
- `$defaultDescription = 'Permet la création de deux dossiers'` : pour gérer le message decrivant la commande lorsque l'on rajoute l'option `--help`;
  - `php bin/console oflix:assets --help`

Si on a besoin d'appeler un service (OmdbAPI, Repository, Manager, ...) à l'interieur de la commande, on est obligé de déclarer un constructeur comme ceci : 

```php
// Lorsque l'on souhaite appeler un Service dans une classe
    // autre qu'un controleur, on est obligé  de créer un constructeur
    // pour effectuer l'injection de ce service
    private $omdbApi;
    private $tvShowRepository;
    private $manager;
    public function __construct(OmdbApi $omdbApi, TvShowRepository $tvShowRepository, EntityManagerInterface $manager)
    {
        // On va pouvoir récupérer les séries
        $this->tvShowRepository = $tvShowRepository;

        // On récupérer les posters pour chaque série
        $this->omdbApi = $omdbApi;

        // On va sauvegarder nos séries en BDD
        $this->manager = $manager;

        // On appelle le constructeur de la classe parent (Command)
        // parce que symfony en a besoin pour vérifier (entre autres) les entrées de la commandes
        parent::__construct();
    }
```

Il ne nous reste plus qu'à coder notre logique métier dans la méthode `execute`.

## Arguments

Un argument est une information que l'on transmet à la commande pour customiser le rendu final.

- `php bin/console oflix:assets argument1`
  - `php bin/console oflix:assets javascripts` : on indique à la commande de créer un dossier `javascripts` grâce à l'argument `javascripts`

Pour configurer des arguments, on modifie la méthode `configure`

```php
protected function configure(): void
    {
        $this
            // InputArgument::OPTIONAL : signifie que l'on peux
            // lancer la commande sans préciser l'argument folder
            // php bin/console oflix:assets

            // InputArgument::REQUIRED : signifie que l'on ne pourra pas 
            // lancer la commande sans préciser l'argument folder
            // php bin/console oflix:assets folder

            // InputArgument::IS_ARRAY : permet de renseigner un nombre illimité d'arguments
            // php bin/console oflix:assets folder1 folder2 folder3

            ->addArgument('folder', InputArgument::OPTIONAL, 'Le dossier que l\'on souhaite créer')

            // ...
    }
```

Pour récupérer la ou les valeurs de notre argument, depuis la méthode `execute`, on appelle la méthode `getArgument`, qui prend en paramètre le nom de l'argument.

```php
$folder = $input->getArgument('folder');
```

- Pour en savoir plus : https://symfony.com/doc/current/console/input.html#using-command-

## Options

Les options sont des informations que l'on transmet à notre commande, mais qui ne sont pas obligatoires.

- `php bin/console oflix:assets --option1`

- InputOption::VALUE_IS_ARRAY : permet de préciser plusieurs options
  - --option="toto" --option="titi"

- InputOption::VALUE_NONE : permet de préciser une option, mais sans indiquer de valeur. Retourne un boolén : vrai quand l'option est indiquer, et false dans le cas contraire.
  - `php bin/console oflix:assets --option1` (true) ou `php bin/console oflix:assets` (false)

- InputOption::VALUE_REQUIRED : si l'on précise une option, on est obligé de renseigner sa valeur
  - `php bin/console oflix:assets --option1=valeur`

- InputOption::VALUE_OPTIONAL : on va pouvoir des options avec ou sans valeur. `--yell ou --yell=loud`

InputOption::VALUE_NEGATABLE : permet de préciser l'option et son contraire


- Pour en savoir plus : https://symfony.com/doc/current/console/input.html#using-command-options