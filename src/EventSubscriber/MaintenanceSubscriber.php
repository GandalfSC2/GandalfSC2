<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event)
    {
        // On récupère la réponse que le serveur s'apprète à retourner
        // au client
        $request = $event->getRequest();

        // On modifie la réponse que le serveur s'apprète à retourner
        // pour y inclure un message de maintenance.
        $onMaintenance = $request->server->get('MAINTENANCE'); // $_ENV['MAINTENANCE']
        $maintenanceMsg = $request->server->get('MAINTENANCE_MSG', 'Maintenance prévue'); // $_ENV['MAINTENANCE_MSG']

        // Bonus : affichage du message en fonction d'une variable
        // d'environnement (.env.local)
        if ($onMaintenance == 1) {
            $response = $event->getResponse();
            $htmlContent = $response->getContent();
            $htmlModified = str_replace('<body>', '<body><div class="mb-0 alert alert-danger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>' . $maintenanceMsg . '</div>', $htmlContent);

            // On met à jour la réponse à retourner au client
            $responseModified = $response->setContent($htmlModified);
            $event->setResponse($responseModified);
        }

        // $event->setResponse(new Response($htmlContent));
    }

    public static function getSubscribedEvents()
    {
        return [
            // Si l'évenement kernel.response 
            // est décleché, Symfony va appeller la méthode onKernelResponse
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
