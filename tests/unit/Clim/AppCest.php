<?php

namespace Clim;

use \UnitTester;

class AppCest
{
    public function _before(UnitTester $I)
    {
        $this->saved_argv = $_SERVER['argv'];
        $_SERVER['argv'] = ['hello_world'];
    }

    public function _after(UnitTester $I)
    {
        $_SERVER['argv'] = $this->saved_argv;
    }

    protected function task() {
        return function ($options, $args) {
            $greeting = $this->has('greeting') ? $this->get('greeting') : 'Hello';
            $name = $args[0] ?: 'world';
            $result = "${greeting} ${name}";

            if ($options['u']) {
                $result = strtoupper($result);
            }

            if ($options['lower']) {
                $result = strtolower($result);
            }

            echo "${result}\n";
        };
    }

    public function testContainer(UnitTester $I)
    {
        $I->wantTo('check basic container interface');

        $app = new App([
            'hello' => 'world',
        ]);

        $container = $app->getContainer();
        $I->assertInstanceOf('\Psr\Container\ContainerInterface', $container);

        $I->assertEquals('world', $container->get('hello'));
    }

    public function simpleApp(UnitTester $I)
    {
        $I->wantTo('test output of simple hello_world');

        $app = new App();

        // $app->option("-h|--help", function(array $options, array $args) {

        // })->description('Display help page');

        $app->task($this->task());

        $I->executeAppAndGetOutput($app, "Hello world\n");
    }

    public function appWithArgument(UnitTester $I)
    {
        $I->wantTo('test output of hello_world with argument');

        $app = new App([
            'argv' => ['hello_world', 'Basuke'],
        ]);

        $app->task($this->task());

        $I->executeAppAndGetOutput($app, "Hello Basuke\n");
    }

    public function appWithContainerBinding(UnitTester $I)
    {
        $I->wantTo('test container binding with closure');

        $app = new App([
            'greeting' => 'Bye',
        ]);

        $app->task($this->task());

        $I->executeAppAndGetOutput($app, "Bye world\n");
    }

    public function appWithSimpleOption(UnitTester $I)
    {
        $I->wantTo('test simple app with option specified');

        $app = new App([
            'argv' => ['hello_world', '-u'],
        ]);

        $app->option('-u');

        $app->task($this->task());

        $I->executeAppAndGetOutput($app, "HELLO WORLD\n");
    }

    public function appWithSimpleOptionButNotSpecified(UnitTester $I)
    {
        $I->wantTo('test simple app which has option but not specified');

        $app = new App([
            'argv' => ['hello_world'],
        ]);

        $app->option('-u');

        $app->task($this->task());

        $I->executeAppAndGetOutput($app, "Hello world\n");
    }

    public function appWithManyOptions(UnitTester $I)
    {
        $I->wantTo('test app which has simple many options');

        $app = new App([
            'argv' => ['hello_world', '-c', '-f', '-a', '-b'],
        ]);

        $app->option('-a');
        $app->option('-b');
        $app->option('-c');
        $app->option('-d');
        $app->option('-e');
        $app->option('-f');

        $app->task(function ($options, $arguments) {
            $result = '';
            foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $opt) {
                if ($options[$opt]) $result .= $opt;
            }
            echo "${result}\n";
        });

        $I->executeAppAndGetOutput($app, "abcf\n");
    }

    public function appWithLongOption(UnitTester $I)
    {
        $I->wantTo('test simple app which has option but not specified');

        $app = new App([
            'argv' => ['hello_world', '--lower'],
        ]);

        $app->option('--lower');

        $app->task($this->task());

        $I->executeAppAndGetOutput($app, "hello world\n");
    }
}
