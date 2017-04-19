<?php

use Clim\Container;
use Psr\Container\ContainerInterface;
use Slim\DeferredCallable;

$I = new UnitTester($scenario);
$I->wantTo('perform actions and see result');

class FooController
{
    private $message;

    public function __construct(ContainerInterface $container)
    {
        $this->message = $container->message;
    }

    public function __invoke()
    {
        return "Foo!";
    }

    public function message()
    {
        return $this->message;
    }
}

// ============================================

// @link https://www.slimframework.com/docs/objects/router.html#container-resolution

$container = new Container(['message' => 'Hello!']);

$container['BarController'] = function($c) {
    return new FooController($c);
};

$callable = new DeferredCallable(FooController::class, $container);
$I->assertEquals("Foo!", $callable());

$callable = new DeferredCallable(FooController::class. ':message', $container);
$I->assertEquals("Hello!", $callable());

$callable = new DeferredCallable('BarController', $container);
$I->assertEquals("Foo!", $callable());

$callable = new DeferredCallable('BarController:message', $container);
$I->assertEquals("Hello!", $callable());

