# Events

Dans le cycle de vie d'une requete, symfony passe par plusieurs étapes appelée `évènements`. A chaque étape (évènement) symfony envoie une notification (prévient) toutes les personnes qui se sont abonnées (les `subscribers`) à l'évènement. 

Il existe 3 catégories d'évènements symfony : 

- Kernel events : kernel.request, kernel.controller, ..., kernel.response 
- Doctrine events : prePersist, postPersist, preUpdate, ..., preRemove, ...- Form events : preSetData, preSubmit, postSubmit, ... 

## Kernel events 

>> Doc : https://symfony.com/doc/current/event_dispatcher.html

Pour s'abonner à un évènement du kernel, on va lancer la commande suivante : 
- `php bin/console make:subscriber`

```bash
php bin/console make:subscriber

 Choose a class name for your event subscriber (e.g. ExceptionSubscriber):
 > RequestDemoSubscriber                  

 Suggested Events:
 * Symfony\Component\Mailer\Event\MessageEvent (Symfony\Component\Mailer\Event\MessageEvent)
 * Symfony\Component\Notifier\Event\MessageEvent (Symfony\Component\Notifier\Event\MessageEvent)
 * Symfony\Component\Security\Http\Event\CheckPassportEvent (Symfony\Component\Security\Http\Event\CheckPassportEvent)
 * Symfony\Component\Security\Http\Event\LoginSuccessEvent (Symfony\Component\Security\Http\Event\LoginSuccessEvent)
 * Symfony\Component\Security\Http\Event\LogoutEvent (Symfony\Component\Security\Http\Event\LogoutEvent)
 * console.command (Symfony\Component\Console\Event\ConsoleCommandEvent)
 * console.error (Symfony\Component\Console\Event\ConsoleErrorEvent)
 * console.terminate (Symfony\Component\Console\Event\ConsoleTerminateEvent)
 * debug.security.authorization.vote (Symfony\Component\Security\Core\Event\VoteEvent)
 * kernel.controller (Symfony\Component\HttpKernel\Event\ControllerEvent)
 * kernel.controller_arguments (Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent)
 * kernel.exception (Symfony\Component\HttpKernel\Event\ExceptionEvent)
 * kernel.finish_request (Symfony\Component\HttpKernel\Event\FinishRequestEvent)
 * kernel.request (Symfony\Component\HttpKernel\Event\RequestEvent)
 * kernel.response (Symfony\Component\HttpKernel\Event\ResponseEvent)
 * kernel.terminate (Symfony\Component\HttpKernel\Event\TerminateEvent)
 * kernel.view (Symfony\Component\HttpKernel\Event\ViewEvent)
 * security.authentication.failure (Symfony\Component\Security\Core\Event\AuthenticationFailureEvent)
 * security.authentication.success (Symfony\Component\Security\Core\Event\AuthenticationEvent)
 * security.interactive_login (Symfony\Component\Security\Http\Event\InteractiveLoginEvent)
 * security.switch_user (Symfony\Component\Security\Http\Event\SwitchUserEvent)

  What event do you want to subscribe to?:
 > kernel.request

 created: src/EventSubscriber/RequestDemoSubscriber.php

           
  Success! 
           

 Next: Open your new subscriber class and start customizing it.
 Find the documentation at https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber
```

```php
<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestDemoSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $server = $request->server;
        $remoteIp = $server->get('REMOTE_ADDR');

        // Si l'adresse est dans une blacklist
        // alors on peut afficher un message d'erreur
        if (in_array($remoteIp, ['189.220.15.3', '125.12.3.6'])) {
            $response = new Response('<h1>Vous ne passerez pas ! </h1>', 403);
            $event->setResponse($response);
        }

        // Sinon, Symfony poursuit les étapes : Etape 2, Etape 3, ...
    }

    public static function getSubscribedEvents()
    {
        // Si l'évènement kernel.request est déclenché
        // Symfony va prévenir la classe RequestDemoSubscriber
        // et appeller la méthode onKernelRequest
        return [
            'kernel.request' => ['onKernelRequest', 7]
        ];
    }
}

```

## Form events 

>> Doc : https://symfony.com/doc/current/form/events.html

```php
$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // ON récupère les données de l'utilisateur que l'on s'apprete
            // à créer ou à éditer
            $user = $event->getData();
            $form = $event->getForm();

            // Si on est dans le cas d'une création de compte utilisateur
            // Alors on ajoute le champs password
            if ($user->getId() === null) {
                $form->add('plainPassword', PasswordType::class, [

                    // On indique à Symfony que la propriété 'plainPassword'
                    // n'est pas liée (mapped) à l'entité User
                    'mapped' => false
                ]);
            }
            // dd($user, $form);
        });
```

## Doctrine events 

Doctrine nous permet d'écouter différents évènement tout au long du cycle de vie d'une entité : 

- prePersist/postPersist : avant ou après la création (`new`) d'une entité
- preUpdate/postUpdate : avant ou après la mise à jour (`update`) d'une entité
- preRemove/postRemove : avant ou après la suppression (`remove`) d'une entité
- ...

### Doctrine Lifecycle Callbacks

Pour se brancher à un évènement doctrine via un lyfecycle CallBacks, on va : 

1. Rajouter l'annotation `@ORM\HasLifecycleCallbacks()` dans la classe de notre entité

```php
/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
 // ...
}
```

2. Créer une nouvelle méthode dans la classe de l'entité concernée et rajouter l'annotation `@ORM\PreUpdate` (dans de l'évènement `preUpdate`)

```php
/**
     * 
     * Cette méthode est appellée par Doctrine
     * lorsque l'évènement preUpdate est déclenché
     * 
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        // Avant de mettre à jour une catégorie en BDD
        // on va d'abord mettre à jour la propriété $updatedAt
        // avec la date du jour
        $this->updatedAt = new DateTimeImmutable();
    }
```

>> Doc : https://symfony.com/doc/current/doctrine/events.html