<?php

namespace Clim;

use \ArrayIterator;
use \Closure;
use \Psr\Container\ContainerInterface;

class App {
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
     * @param ContainerInterface|array|null $container
     */
    public function __construct($container = null)
    {
        if (!is_a($container, '\Psr\Container\ContainerInterface')) {
            $container = new \Slim\Container((array) $container);
        }

        if (!$container->has('argv')) {
            $container['argv'] = $_SERVER['argv'];
        }

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

    public function dispatch($option, $callable)
    {
    }

    public function task($callable)
    {
        $this->task = $this->containerBoundCallable($callable);
    }

    public function run()
    {
        $runner = new Runner($this->parsers, $this->handlers);
        $context = $runner->run($this->getContainer()->get('argv'));

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