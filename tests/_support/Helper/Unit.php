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

    public function executeAppAndGetOutput($app, $expected)
    {
        $output = $this->captureOutput(function () use ($app) {
            $app->run();
        });

        $asserts = $this->getModule('Asserts');
        $asserts->assertEquals($expected, $output);
    }
}
