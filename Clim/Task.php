<?php

namespace Clim;

use Clim\Helper\ArgumentsSupplier;
use Clim\Helper\Hash;
use Closure;
use Psr\Container\ContainerInterface;
use Slim\CallableResolverAwareTrait;

class Task
{
    use CallableResolverAwareTrait;

    private $callable;

    private $container;

    /**
     * @param callable|string $callable
     * @param ContainerInterface $container
     */
    public function __construct($callable, ContainerInterface $container)
    {
        $this->callable = $callable;
        $this->container = $container;
    }

    public function execute(Context $context)
    {
        $callable = $this->resolveCallable($this->callable);
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this->container);

            $supplier = new ArgumentsSupplier($context, $this->container);
            $args = $supplier->supplyFor($callable);
        } else {
            $options = $context->options();
            $arguments = $context->arguments();

            $args = [new Hash($options), new Hash($arguments)];
        }

        return call_user_func_array($callable, $args);
    }
}
