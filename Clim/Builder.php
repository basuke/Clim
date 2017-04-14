<?php

namespace Clim;

use \Closure;
use \Psr\Container\ContainerInterface;

class Builder
{
    /** @var ContainerInterface $container */
    private $container;

    /** @var Closure $task */
    private $task;

    /** @var array $parsers */
    private $parsers = [];

    /** @var array $handlers */
    private $handlers = [];

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

        if (is_null($callable)) {
            $flags |= Option::TYPE_BOOL;
        }

        $parser = new OptionParser($option, $flags, $this->containerBoundCallable($callable));
        $this->parsers[] = $parser;
        return $parser;
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
        $this->task = $this->containerBoundCallable($callable);
    }

    protected function runner()
    {
        return new Runner($this->parsers, $this->handlers);
    }

    public function run(Context $context)
    {
        $this->runner()->run($context);

        if ($this->task) {
            call_user_func($this->task, new Collection($context->options()), new Collection($context->arguments()));
        }
    }

    protected function containerBoundCallable($callable)
    {
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this->container);
        }
        return $callable;
    }
}