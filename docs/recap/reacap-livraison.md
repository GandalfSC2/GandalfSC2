# Livraison du projet

## Etapes avant la livraison

1. On vérifie que le code fonctionne ==> tests unitaires et tests fonctionnels
2. On créé une base de données de production vide, pour vérifie que notre application fonctionne même s'il n y a pas ou peu de données
   1. Dans le fichier `.env.local`, on modifie le nom de la table en rajoutant le suffixe `_prod` (`oflix_prod`)
   2. Ensuite, on crée la table, que l'on remplit avec des fixtures
      1. `php bin/console d:d:c`
      2. `php bin/console make:migration` ou  `php bin/console ma:mi`
      3. `php bin/console doctrine:migration:migrate` ou  `php bin/console d:mi:mi`
3. On install le composant [apache-pack](https://symfony.com/doc/current/setup/web_server_configuration.html)
   1. `composer require symfony/apache-pack`
4. On merge (si nécessaire) et on push sur un serveur Git