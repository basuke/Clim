<?php

use Clim\Context;
use Clim\OptionHandler;
use Clim\Runner;

$I = new UnitTester($scenario);
$I->wantTo("define Runner features");

$runner = new Runner([
    (new OptionHandler('-a')),
    (new OptionHandler('-b|--boo')),
    (new OptionHandler('--case {CASE}')),
    (new OptionHandler('-d|--do {ACTION}')),
]);

$context = $runner->run(['program_name', 'hello', '-a', '--do', 'foo', 'bar']);
$I->assertTrue($context['a']);
$I->assertEquals($context[0], 'hello');
$I->assertEquals($context[1], 'bar');
$I->assertFalse(isset($context[3]));
$I->assertEquals($context->options(), ['a' => true, 'do' => 'foo']);
$I->assertEquals($context->arguments(), ['hello', 'bar']);

$context = $runner->run(['program_name', '--case', '1234', '--boo']);
$I->assertFalse(isset($context[0]));
$I->assertEquals($context->options(), ['boo' => true, 'case' => '1234']);
$I->assertEquals($context->arguments(), []);

$context = $runner->run(['program_name', '--case=1234', '-abd', 'dance', 'bingo']);
$I->assertEquals($context[0], 'bingo');
$I->assertEquals($context->options(), ['case' => '1234', 'a' => true, 'b' => true, 'd' => 'dance']);
$I->assertEquals($context->arguments(), ['bingo']);

