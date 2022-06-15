<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Persistence\ManagerRegistry;

class TokenSubscriber implements EventSubscriberInterface
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {
            
            $request = $event->getRequest();
            
            $apiToken = $request->isMethod('GET') ? $request->query->get('api_token') : $request->get('api_token');
        
            // If exception is thrown, onAuthenticationFailure is called.
            if (null === $apiToken) {
                throw new AccessDeniedHttpException('No API token provided.');
            }
    
            if (!is_string($apiToken)) {
                throw new AccessDeniedHttpException('API token must be string.');
            }
    
            $user = $this->doctrine->getRepository(User::class)->findOneByApiToken($apiToken);
    
            if (null === $user) {
                throw new AccessDeniedHttpException('No user is matched by the API token.');
            }
            
            // Set attributes allows for passing data from event subscriber to controller.
            // This means that there is no need of additional SQL requests to extract the user.
            $event->getRequest()->attributes->set('api_token_user', $user);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}