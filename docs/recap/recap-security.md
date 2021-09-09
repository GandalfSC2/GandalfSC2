## Sécurité et User

### a) Configuration

- Tout ce qui concerne la sécurité se trouve dans security.yaml

### b) Création de l'entité User + Formulaire de login

On commence par créer une entité User avec la commande : `php bin/console make:user` 

Rajouter éventuellement des proprirétés à cette entité User (firstname, lastname, ..).

Mettre à jour la base de données pour prendre en compte la création/mise à jour de l'entité 

- `php bin/console doctrine:schema:update --force`

Pour créer le formulaire d'authentification

- Faire `php bin/console make:auth`
- Choisir 1 pour login
- Appeler la classe `LoginFormAuthenticator`
- Appeler le controlleur `SecurityController`
- Mettre en place des accès dans `security.yaml`, dans la partie access_control, rajoutez la ligne : `- { path: ^/backoffice$, roles: ROLE_ADMIN }`

### c) Pour générer le hash d'un password pour le rentrer à la main dans la BDD

`php bin/console security:hash-password`

### d) Indiquer la route une fois le login fait

Sur LoginFormAuthenticator, il faut juste indiquer le nom de la route sur laquelle rediriger un user qui se connecte sur dans la méthode `onAuthenticationSuccess` et supprimer la ligne qui fait un throw d'une erreur

```php
     public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Si la connexion est réussie, on redirige vers la page
        // d'accueil
        return new RedirectResponse($this->urlGenerator->generate('home'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }
```

### e) Récupérer le user 

- Depuis un controller, faire un `$this->getUser();`
- Depuis un template twig, faire un `{{ app.user }}`


### f) Faire le CRUD d'un user

#### En passant par make:crud

La commande est simple : `make:crud User`

- Si en BDD on a un tableau pour les roles, il peut y avoir une erreur sur le formulaire d'ajout d'un user
- Pour le corriger il faut, sur `UserType.php`, faire les modifications ci-dessous
  - Définir le `ChoiceType::class`
  - Définir le tableau des choix possibles `'choices' => []`
  - Définir l'option pour pouvoir choisir plusieurs valeurs `'multiple' => true`
  - Définir l'option pour les afficher sous forme de checkbox `'expand' => true`

- Par défaut la confidentialité des mots de passe n'est pas configurée, il faut modifier ça :
  - Dans `UserType`, il faut changer la configuration de `password` : `->add('password', PasswordType::class)`, çe met un champ password dans le form
  - Puis dans le `UserController`, il faut lui dire de hasher le password (Dans `new` et `edit`)
    - On récupère le password dans le user : `$password = $user->getPassword();`
    - On hashe le password : 
      - Ajouter le use : `use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;`
      - Ajouter `UserPasswordHasherInterface $passwordHasher` en injection de dépendance au controller
      - Rajouter dans le controller `$hashedPassword  = $passwordHasher->hashPassword($user, $plainPassword);`
    - On set le nouveau mot de passe dans le user : `$user->setPassword($hashedPassword);`
    - On peut aussi tout regrouper dans une seule instruction :
  
```php
     $user->setPassword(
        $passwordHasher->hashPassword(
            $user,
            $form->get('plainPassword')->getData()
        )
    );
```

- Si on veut mettre deux champs password pour le confirmer, sur UserType : 
  
  - On met le type `RepeatedType` sur le add password
  - On rajoute une option pour indiquer que ce sont des champs de password : `'type' => PasswordType::class,`
  - On indique le label du premier champ : `'first_options'  => ['label' => 'Mot de passe'],`
  - On indique le label du deuxième champ : `'second_options' => ['label' => 'Veuillez répétez le mot de passe'],`
  - On personnalise le message d'erreur : `'invalid_message' => 'Les deux mots de passe doivent être identiques',`

### e) Les roles des users

- Par defaut, tous les users ont le `ROLE_USER`, c'est défini ainsi dans la méthode `getRoles` de l'entité `User.php`
- On peut créer les roles que l'on veut. La seule règle de bonne pratique, c'est de tout écrire en majuscule avec en préfixe `ROLE_`

#### Définir les roles dans security.yaml

- Les roles se définissent dans `security.yaml`
- Dans la partie `access_control`
- Créer une ligne par règle d'accès :
  - Pour que tout le monde ait accès au login : `{ path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }`
  - Exemple accès aux pages de séries pour les users : `{ path: ^/show/list, roles: ROLE_USER }`
  - Exemple accès aux pages du backoffice pour les admins `{ path: ^/admin, roles: ROLE_ADMIN }`
  - Exemple d'accès sur des méthodes `{ path: ^/, methods: ["DELETE"], roles: ROLE_ADMIN }`

- Il est possible de hiérarchiser les roles
- Dans `security.yaml` toujours, rajouter après le bloc `acces_control`, un bloc `role_hierarchy`
- On peut définir des hiérarchies de user
  - Par exemple : `ROLE_ADMIN: ROLE_USER` , ici ROLE_ADMIN est au dessus de ROLE_USER, il hérite donc de tous ses accès

#### Définir des roles directement dans les controllers

Dans certains cas, on ne voudra pas restreindre les accès dans security.yaml via une route mais directement sur un controller.

Ca peut être utile, si on veut ajouter des conditions sur les accès. 

Par exemple, on peut supprimer une série uniquement si elle a été créé il y a moins de trois jours, et il faut en plus être admin pour le faire.

On pourrait mettre en place cette règle directement depuis le controleur. Mais que se passerait-il si besoin de l'appliquer ailleur ? On fait un copier-coller ? Surement pas malheureux !

On passe par des voters :

- Pour créer des voters : `php bin/console make:voter`
- Lui donner un nom.
- Et le reste de la configuration se fait dans le fichier créé dans `src/Security/Voter` . Voir exemple dans le projet O'flix
- On va créer les logiques d'accès dans la fonction voteOnAttribute

Le voter est appelé grâce à le denyAccessOrGranted dans le controller. Il va aller chercher s'il y a un voter qui existe pour l'entité en question

Dans le voter, il y a deux étapes :

- En premier lieu, la méthode supports de chaque Voter est appellée par Symfony. Le premier voter qui retourne un booléen à TRUE est sélectionné. On passe ensuite à la deuxième étape , le voteOnAttribute
- Dans ce voteOnAttribute : 
  - On indique les logiques d'autorisation pour chacun des cas à tester
  - Pour chaque cas, on retourne un true or false selon les conditions que l'on veut accéder

#### Indiquer des roles dans les annotations

On peut indiquer un role admin sur un controller directement depuis les annotations avec la ligne : 

`@IsGranted("ROLE_ADMIN")` , par exemple

En rajoutant le use nécessaire : `use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;`

Doc : https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html#annotations-for-controllers

#### Récupérer les roles dans twig

Avec la fonction `{{ is_granted('ROLE_USER') }}`, et on modifie le type de rôle selon nos besoins. Répond un booléen pour pouvoir faire des conditions d'affichage
