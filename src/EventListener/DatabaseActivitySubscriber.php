<?php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class DatabaseActivitySubscriber
 * @package App\EventListener
 */
class DatabaseActivitySubscriber implements EventSubscriber
{
    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $cache;

    /**
     * DatabaseActivitySubscriber constructor.
     * @param AdapterInterface $cache
     */
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postFlush
        ];
    }

    /**
     *
     */
    public function postFlush(): void
    {
        $this->cache->clear();
    }
}
