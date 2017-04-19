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

        $app = $I->createAnApp([
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

        $app->task($this->task());

        $I->assertOutputEquals($app, "Hello world\n");
    }

    public function appWithArgument(UnitTester $I)
    {
        $I->wantTo('test output of hello_world with argument');

        $app = $I->createAnApp([
            'argv' => ['hello_world', 'Basuke'],
        ]);

        $app->task($this->task());

        $I->assertOutputEquals($app, "Hello Basuke\n");
    }

    public function appWithContainerBinding(UnitTester $I)
    {
        $I->wantTo('test container binding with closure');

        $app = new App([
            'greeting' => 'Bye',
        ]);

        $app->task($this->task());

        $I->assertOutputEquals($app, "Bye world\n");
    }

    public function appWithSimpleOption(UnitTester $I)
    {
        $I->wantTo('test simple app with option specified');

        $app = $I->createAnApp([
            'argv' => ['hello_world', '-u'],
        ]);

        $app->option('-u');

        $app->task($this->task());

        $I->assertOutputEquals($app, "HELLO WORLD\n");
    }

    public function appWithSimpleOptionButNotSpecified(UnitTester $I)
    {
        $I->wantTo('test simple app which has option but not specified');

        $app = $I->createAnApp([
            'argv' => ['hello_world'],
        ]);

        $app->option('-u');

        $app->task($this->task());

        $I->assertOutputEquals($app, "Hello world\n");
    }

    public function appWithManyOptions(UnitTester $I)
    {
        $I->wantTo('test app which has simple many options');

        $app = $I->createAnApp([
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

        $I->assertOutputEquals($app, "abcf\n");
    }

    public function appWithLongOption(UnitTester $I)
    {
        $I->wantTo('test simple app which has option but not specified');

        $app = $I->createAnApp([
            'argv' => ['hello_world', '--lower'],
        ]);

        $app->option('--lower');

        $app->task($this->task());

        $I->assertOutputEquals($app, "hello world\n");
    }

    public function appWithMiddleware(UnitTester $I)
    {
        $I->wantTo('test simple app which has option but not specified');

        $app = $I->createAnApp([
            'argv' => ['hello_world', '--lower'],
        ])->add(function ($context, $next) {
            echo "Before\n";
            $next($context);
            echo "After\n";
            return $context;
        });

        $app->option('--lower');

        $app->task($this->task());

        $I->assertOutputEquals($app, "Before\nhello world\nAfter\n");
    }

    public function accessToContainerFromMiddleware(UnitTester $I)
    {
        $container = new \Clim\Container([
            'argv' => ['hello_world'],
            'message' => 'Welcome!!!'
        ]);
        $app = new App($container);

        $app->add(function ($context, $next) {
            if (isset($this->message)) echo $this->message . "\n";
            $next($context);
            return $context;
        });

        $app->task($this->task());

        $I->assertOutputEquals($app, "Welcome!!!\nHello world\n");
    }

    public function ifExceptionHappens(UnitTester $I)
    {
        $I->wantTo('test error case. Exception should be handled by App.');

        $app = new App(['argv' => ['hello_world']]);
        $app->task(function ($opt, $arg) {
            throw new \Exception("Bad thing", 123);
        });

        $I->expectException(\Exception::class, function () use ($app) {
            $context = $app->getContainer()->get('context');
            $app->runner()->run($context);
        });
    }
}
