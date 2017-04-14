<?php

namespace Clim;

use \Closure;
use \Psr\Container\ContainerInterface;

class Builder
{
    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $parsers = [];

    /** @var array */
    private $handlers = [];

    /** @var array */
    private $tasks = [];

    /**
     * Constructor of Clim\App
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        $parser = new OptionParser($option, $flags, $this->containerBoundCallable($callable));
        $this->parsers[] = $parser;
        return $parser;
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

        $handler = new ArgumentHandler($name, $flags, $this->containerBoundCallable($callable));
        $this->handlers[] = $handler;
        return $handler;
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
        $this->handlers[] = $dispatcher;
        return $dispatcher;
    }

    public function task($callable)
    {
        $this->tasks[] = $this->containerBoundCallable($callable);
    }

    public function runner()
    {
        return new Runner($this->parsers, $this->handlers, $this->tasks);
    }

    protected function containerBoundCallable($callable)
    {
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this->container);
        }
        return $callable;
    }
}