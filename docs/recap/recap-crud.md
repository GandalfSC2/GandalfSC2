# Création d'un CRUD d'une entité

## `make:crud`

- `php bin/console make:crud`

> Question  1 : Quelle entité ==> Category
> Question 2  : Quelle nom de controleur ==> Backoffice\Category 
    > src/Controller/Backoffice/CategoryController.php
    > src/CategoryType.php
    > templates/backoffice/category/{index.html.twig, show.html.twig, ...}

On ensuite accès au crud via l'url : `/backoffice/category`

- On peaufine en rajoutant des messages flash, du style, ...
- On vérifie que les pages fonctionnent comme prévu


Et en cas d'erreur d'affichage d'une variable complexe comme un objet, on peut utiliser la méthode `__toString`.  

Par défaut, PHP ne peut pas tout afficher le contenu d'un objet : un objet possède des propriétés (couleur, un nom, ...). 

Comme demander l'affichage un objet n'est pas suffisament précis, on risque de tomber sur un bug du type : 

- `Object of class App\Entity\Character could not be converted to string`

On peut appeler dans l'entité `Character` la méthode magique `_toString()` en la définissant comme ceci : 

```php
public function __toString() {
    return $this->firstname;
}
```