<?php

namespace App\EventListener;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    protected $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($event->getRequest()->attributes->get('_route') == "login_check") {
            $this->cache->clear();
        }
    }
}
