<?php


namespace App\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class DatabaseActivitySubscriber implements EventSubscriber
{
    protected AdapterInterface $cache;

    public function __construct(AdapterInterface $cache){
        $this->cache = $cache;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postFlush
        ];
    }

    public function postFlush(): void
    {
        $this->cache->clear();
    }
}
