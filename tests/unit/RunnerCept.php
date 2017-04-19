<?php

use Clim\Context;
use Clim\Option;
use Clim\Runner;

$I = new UnitTester($scenario);
$I->wantTo("define Runner features");

/**
 * application with some option arguments
 */

$runner = new Runner([
    (new Option('-a')),
    (new Option('-b|--boo')),
    (new Option('--case {CASE}')),
    (new Option('-d|--do {ACTION}')),
], [
    // no arguments
]);

$context = $runner->run(['hello', '-a', '--do', 'foo', 'bar']);
$I->assertEquals([
    'hello',
    'bar'
], $context->arguments());
$I->assertEquals([
    'a' => true,
    'd' => 'foo',
    'do' => 'foo',
], $context->options());
$I->assertEquals(['hello', 'bar'], $context->arguments());

$context = $runner->run(['--case', '1234', '--boo']);
$I->assertEquals($context->arguments(), []);
$I->assertEquals([
    'boo' => true,
    'b' => true,
    'case' => '1234',
], $context->options());
$I->assertEquals([], $context->arguments());

$context = $runner->run(['--case=1234', '-abd', 'dance', 'bingo']);
$I->assertEquals([
    'case' => '1234',
    'a' => true,
    'b' => true,
    'boo' => true,
    'd' => 'dance',
    'do' => 'dance',
], $context->options());
$I->assertEquals(['bingo'], $context->arguments());

//========================================
// application with default option value
//========================================

$runner = new Runner([
    (new Option('-c|--case {CASE}'))->default('abc'),
], [
]);

$context = $runner->run([]);
$I->assertEquals([
    'case' => 'abc',
    'c' => 'abc',
], $context->options());
