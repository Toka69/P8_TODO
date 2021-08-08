<?php

namespace App\HandlerFactory;

 use Psr\Container\ContainerInterface;

 /**
  * Class HandlerFactory
  * @package App\HandlerFactory
  */
class HandlerFactory implements HandlerFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * HandlerFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $handler
     * @return HandlerInterface
     */
    public function createHandler(string $handler): HandlerInterface
    {
        return $this->container->get($handler);
    }
}
