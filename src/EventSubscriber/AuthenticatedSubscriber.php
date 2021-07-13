<?php


namespace App\EventSubscriber;


use App\Controller\AuthenticatedController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AuthenticatedSubscriber implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $controllerEvent)
    {
        $controller = $controllerEvent->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof AuthenticatedController) {
            if ($this->security->isGranted("ROLE_USER") === false) {
                $controllerEvent->setController(function() {
                    return new RedirectResponse('/login');
                });
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
