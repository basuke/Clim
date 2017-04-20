<?php

use Clim\App;
use Clim\Argument;
use Clim\Cli\Parameters;
use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo('define how ArgumentParser works');

// ============================================

$argument1 = new Argument('arg1');

$argument2 = new Argument('arg2');
$argument2->multiple();

$parameters = new Parameters([]);
$context = new Context();

$argument1->handle('hello', $parameters, $context);

$I->assertEquals($argument1->metaVar(), 'arg1');
$I->assertEquals($context->arguments(), [
    0 => 'hello',
    'arg1' => 'hello'
]);

// ============================================
// multiple arguments

$parameters = new Parameters(['world', 'again']);
$context = new Context();

$argument2->handle('hello', $parameters, $context);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'world',
    2 => 'again',
    'arg2' => ['hello', 'world', 'again']
]);

// ============================================
// multiple arguments stops on option

$parameters = new Parameters(['world', '--foo']);
$context = new Context();

$argument2->handle('hello', $parameters, $context);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'world',
    'arg2' => ['hello', 'world']
]);

// ============================================
// multiple arguments after --

$parameters = new Parameters(['--', 'world', '--foo', 'again', '--']);
$context = new Context();

$argument2->handle('hello', $parameters, $context);

$I->assertEquals($context->arguments(), [
    0 => 'hello',
    1 => 'world',
    2 => '--foo',
    3 => 'again',
    4 => '--',
    'arg2' => ['hello', 'world', '--foo', 'again', '--']
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

