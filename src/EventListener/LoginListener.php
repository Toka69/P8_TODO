<?php

namespace App\EventListener;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class LoginListener
 * @package App\EventListener
 */
class LoginListener
{
    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $cache;

    /**
     * LoginListener constructor.
     * @param AdapterInterface $cache
     */
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($event->getRequest()->attributes->get('_route') == "login_check") {
            $this->cache->clear();
        }
    }
}
