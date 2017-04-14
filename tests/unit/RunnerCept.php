<?php

use Clim\Context;
use Clim\OptionParser;
use Clim\Runner;

$I = new UnitTester($scenario);
$I->wantTo("define Runner features");

$runner = new Runner([
    (new OptionParser('-a')),
    (new OptionParser('-b|--boo')),
    (new OptionParser('--case {CASE}')),
    (new OptionParser('-d|--do {ACTION}')),
], [
]);

$context = $runner->run(['hello', '-a', '--do', 'foo', 'bar']);
$I->assertEquals($context->arguments(), [
    'hello',
    'bar'
]);
$I->assertEquals($context->options(), [
    'a' => true,
    'd' => 'foo',
    'do' => 'foo',
]);
$I->assertEquals($context->arguments(), ['hello', 'bar']);

$context = $runner->run(['--case', '1234', '--boo']);
$I->assertEquals($context->arguments(), []);
$I->assertEquals($context->options(), [
    'boo' => true,
    'b' => true,
    'case' => '1234',
]);
$I->assertEquals($context->arguments(), []);

$context = $runner->run(['--case=1234', '-abd', 'dance', 'bingo']);
$I->assertEquals($context->options(), [
    'case' => '1234',
    'a' => true,
    'b' => true,
    'boo' => true,
    'd' => 'dance',
    'do' => 'dance',
]);
$I->assertEquals($context->arguments(), ['bingo']);

