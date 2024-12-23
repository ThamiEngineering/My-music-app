<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CheckBannedUserSubscriber implements EventSubscriberInterface
{
    private $security;
    private $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        // Vérifiez si l'utilisateur est banni et s'il n'est pas déjà sur la route /banned
        if ($user && in_array('IS_BANNED', $user->getRoles()) && $request->get('_route') !== 'banned') {
            $route = $this->router->generate('banned');
            $event->setResponse(new RedirectResponse($route));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
