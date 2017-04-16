<?php
$I = new UnitTester($scenario);
$I->wantTo('test Clim\Container behavior');

$container = new \Clim\Container([
    'hello' => 'world',
]);

$I->assertTrue($container instanceof \Psr\Container\ContainerInterface);
$I->assertEquals($container->get('hello'), 'world');

$I->assertTrue($container->has('argv'));

$I->assertTrue($container->get('callableResolver') instanceof \Slim\CallableResolver);