<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Clim\App;
use Clim\Container;
use Clim\Context;
use Codeception\Module;

class Unit extends Module
{
    public function captureOutput($callable)
    {
        ob_start();
        $callable();
        return ob_get_clean();
    }

    public function createAnApp(array $config = [])
    {
        $container = new Container($config);
        return new App($container);
    }

    public function assertOutputEquals(App $app, $expected)
    {
        $output = $this->captureOutput(function () use ($app) {
            $context = $app->getContainer()->get('context');
            $app->runner()->run($context);
        });

        $asserts = $this->getModule('Asserts');
        $asserts->assertEquals($expected, $output);
    }

    public function willPassArguments(App $app, $argv)
    {
        $container = $app->getContainer();
        $container['argv'] = $argv;

        $container['context'] = new Context(array_slice($argv, 1));
    }
}
