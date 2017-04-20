<?php

use Clim\App;
use Clim\Argument;
use Clim\Cli\Parameters;
use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo('define how ArgumentParser works');

// ============================================

$argument = new Argument('arg1');
$parameters = new Parameters([]);
$context = new Context();

$argument->handle('hello', $parameters, $context);

$I->assertEquals($argument->metaVar(), 'arg1');
$I->assertEquals($context->arguments(), [
    0 => 'hello',
    'arg1' => 'hello'
]);

// ============================================

$argument = new Argument('arg2');
$argument->multiple();

$parameters = new Parameters(['world', 'again']);
$context = new Context();

$argument->handle('hello', $parameters, $context);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'world',
    2 => 'again',
    'arg2' => ['hello', 'world', 'again']
]);

// ============================================
// this app accept two arguments. The first one
// is required and second one has default value.

$app = new App();
$app->argument('arg1');
$app->argument('arg2')->default('universe');

// ============================================
// defined arguments and undefined argument

/** @var Context $context */
$context = $app->runner()->run(['hello', 'world', 'again']);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'world',
    2 => 'again',
    'arg1' => 'hello',
    'arg2' => 'world'
]);

// ============================================
// default argument

/** @var Context $context */
$context = $app->runner()->run(['hello']);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'universe',
    'arg1' => 'hello',
    'arg2' => 'universe'  // <- default
]);

// ============================================
// required argument

$I->expectException(\Clim\Exception\ArgumentRequiredException::class, function () use ($app) {
    $context = $app->runner()->run([]);
});

