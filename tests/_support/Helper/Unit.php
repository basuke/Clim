<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    public function captureOutput($callable)
    {
        ob_start();
        $callable();
        return ob_get_clean();
    }

    public function createAnApp(array $config = [])
    {
        $container = new \Slim\Container($config);
        return new \Clim\Builder($container);
    }

    public function assertOutputEquals(\Clim\Builder $app, $expected)
    {
        $output = $this->captureOutput(function () use ($app) {
            $argv = $app->getContainer()->get('argv');
            $app->runner()->run(array_slice($argv, 1));
        });

        $asserts = $this->getModule('Asserts');
        $asserts->assertEquals($expected, $output);
    }

    public function willPassArguments($app, $argv)
    {
        $container = $app->getContainer();
        $container['argv'] = $argv;
    }
}
