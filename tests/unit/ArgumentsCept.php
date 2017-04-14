<?php

use Clim\App;
use Clim\ArgumentHandler;
use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo('define how ArgumentParser works');

$handler = new ArgumentHandler('arg1');

$context = new Context();

$handler->handle('hello', $context);

$I->assertEquals($handler->metaVar(), 'arg1');
$I->assertEquals($context->arguments(), [
    0 => 'hello',
    'arg1' => 'hello'
]);

$app = $I->createAnApp();
$app->argument('arg1');
$app->argument('arg2');

$context = $app->runner()->run(['hello', 'world', 'again']);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'world',
    2 => 'again',
    'arg1' => 'hello',
    'arg2' => 'world'
]);

$I->expectException(\Clim\Exception\ArgumentRequiredException::class, function () use ($app) {
    $context = $app->runner()->run([]);
    var_dump($context);
});