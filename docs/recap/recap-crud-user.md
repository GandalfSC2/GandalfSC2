# Création d'un CRUD User

## `php bin/console make:crud`

```php
php bin/console make:crud

 The class name of the entity to create CRUD (e.g. GrumpyKangaroo):
 > User

 Choose a name for your controller class (e.g. UserController) [UserController]:
 > Backoffice\User

 created: src/Controller/Backoffice/UserController.php
 created: src/Form/UserType.php
 created: templates/backoffice/user/_delete_form.html.twig
 created: templates/backoffice/user/_form.html.twig
 created: templates/backoffice/user/edit.html.twig
 created: templates/backoffice/user/index.html.twig
 created: templates/backoffice/user/new.html.twig
 created: templates/backoffice/user/show.html.twig

           
  Success! 
           

 Next: Check your new CRUD by going to /backoffice/user/
```

La crud génère la gestion basique des utilisateurs (création, update, affichage et suppression), sans inclure la logique de hashage de mot de passe.

Pour gérer le hashage de mot de passe dans les méthodes `new` et `edit` du CRUD User, on va : 
- Mettre à jour le `UserType` pour inclure un champ `plainPassword` déconnecté de l'entité `User` (`mapped` à `false`)
- Mettre à jour le `UserController`

## Mise à jour du `UserType`

On va remplacer le champ `password` par un champ `plainPassword` (Nom choisi de façon arbitraire). Ce champ permettra de stocker le mot de passe en clair, qui sera ensuite hashé dans le controleur. 

```php
 // ->add('password')
->add('plainPassword', PasswordType::class, [

    // On indique à Symfony que la propriété 'plainPassword'
    // n'est pas liée (mapped) à l'entité User
    'mapped' => false
])
```

Sans le oublier le use qui va bien : `use Symfony\Component\Form\Extension\Core\Type\PasswordType;`

## Mise à jour du controleur

On va récupérer le mot de passe en clair, le hasher et ensuite mettre à jour notre entité `User` avec la méthode `setPassword`

```php
// On récupère le mot de passe en clair
$plainPassword = $form->get('plainPassword')->getData();

// On hash le mot de passe
$hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

// On met à jour l'entité User
$user->setPassword($hashedPassword);
```

Voir méthodes `new` et `edit` du UserController (`src/Backoffice/UserController`) et `register` du `RegistrationController` (`src/RegistrationController.php`);