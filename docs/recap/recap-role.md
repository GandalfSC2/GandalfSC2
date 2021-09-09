# Roles en Symfony

La gestion peut se faire depuis le fichier `config/package/security.yaml`

Pour créér un nouveau rôle, il faut le préfixer de `ROLE_`.
Ex : `ROLE_ADMIN`, `ROLE_TVSHOW_EDITOR`, `ROLE_EDITOR`

```yaml
access_control:
    # Il faut être connecté (ROLE_USER) pour afficher les pages /tvshow
    - { path: ^/tvshow, roles: ROLE_USER }

    # Il faut avoir le rôle ROLE_ADMIN pour afficher le backoffice
    - { path: ^/backoffice, roles: ROLE_ADMIN }
```

Quand on est authentifié, on a au minimum un rôle `ROLE_USER` ==> Voir méthode `getRoles` de l'entité `User`.
