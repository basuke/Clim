<?php

namespace Clim;

use \Psr\Container\ContainerInterface;

class App extends Builder
{
    /**
     * Constructor of Clim\App
     * @param ContainerInterface|array|null $container
     */
    public function __construct($container = null)
    {
        if (!is_a($container, '\Psr\Container\ContainerInterface')) {
            $container = new Container((array) $container);
        }

        parent::__construct($container);
    }

    public function run()
    {
        $argv = $this->getContainer()->get('argv');
        $context = new Context(array_slice($argv, 1));

        $this->runner()->run($context);
    }
}