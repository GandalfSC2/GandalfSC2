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
