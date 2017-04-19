<?php

namespace Clim;

use Clim\Middleware\MiddlewareStack;
use Closure;
use Psr\Container\ContainerInterface;
use Slim\DeferredCallable;

class Builder
{
    /** @var ContainerInterface */
    private $container;

    /** @var Runner */
    private $runner;

    /**
     * Constructor of Clim\App
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->runner = new Runner();
        $this->runner->setApp($this);
    }

    /**
     * return application container
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $option
     * @param Closure|null $callable
     */
    public function option($option, $callable = null)
    {
        $flags = 0;

        $option = new Option($option, $flags, $this->containerBoundCallable($callable));
        $this->runner->addOption($option);
        return $option;
    }

    /**
     * Alias of option()
     * @param string $option
     * @param Closure|null $callable
     */
    public function opt($option, $callable = null)
    {
        return $this->option($option, $callable);
    }

    /**
     * @param string $name
     * @param Closure|null $callable
     */
    public function argument($name, $callable = null)
    {
        $flags = 0;

        $argument = new Argument($name, $flags, $this->containerBoundCallable($callable));
        $this->runner->addArgument($argument);
        return $argument;
    }

    /**
     * Alias of argument()
     * @param string $name
     * @param Closure|null $callable
     */
    public function arg($name, $callable = null)
    {
        return $this->argument($name, $callable);
    }

    /**
     * @param
     */
    public function dispatch($meta_var, $children)
    {
        $dispatcher = new Dispatcher($meta_var, $children, $this->getContainer());
        $this->runner->addArgument($dispatcher);
        return $dispatcher;
    }

    /**
     *
     * @since 1.1.0
     */
    public function add($callable)
    {
        $this->runner->pushMiddleware($this->containerBoundCallable($callable));
        return $this;
    }

    public function task($callable)
    {
        $this->runner->addTask($this->containerBoundCallable($callable));
    }

    public function runner()
    {
        return $this->runner;
    }

    protected function containerBoundCallable($callable)
    {
        if (is_null($callable)) return null;

        // if ($callable instanceof Closure) {
        //     $callable = $callable->bindTo($this->container);
        // }
        return new DeferredCallable($callable, $this->container);
    }

    protected function validateMiddlewareContext(ContextInterface $context)
    {
            if ($result instanceof ContextInterface === false) {
                $context->setOutput($context);
            }
    }
}