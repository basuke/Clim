<?php

use \Clim\Context;
use \Clim\Option;

$I = new UnitTester($scenario);
$I->wantTo('define Option features');

// ============================================
// see the option matches with short option

$option = new Option('-f');
$I->assertTrue($option->match('f'));

// ============================================
// see the option matches with long option

$option = new Option('--foo');

$I->assertTrue($option->match('foo'));

// ============================================
// see the option matches with long option by
// mixing the definition

$option = new Option('-f|--foo');

$I->assertTrue($option->match('f'));
$I->assertTrue($option->match('foo'));
$I->assertFalse($option->match('hello'));

// ============================================
// see the option matches with option with value

$option = new Option('-k {VALUE}');

$I->assertTrue($option->needValue());
$I->assertEquals('VALUE', $option->metaVar());

// ============================================
// see the option matches with mixed option with value

$option = new Option('-t|--time {TIME_STR}');

$I->assertTrue($option->needValue());
$I->assertEquals('TIME_STR', $option->metaVar());

// ============================================
// see the option works with option with
// extra value

$option = new Option('-t|--time {TIME_STR}');
$context = new Context(['today', 'tomorrow']);
$parameters = $context->parameters;

$I->assertTrue($option->parse('t', $context, $parameters));
$I->assertEquals('today', $context->options()['time']);
$I->assertEquals('tomorrow', $context->parameters->next());

// ============================================
// see the option works with option with
// tentative value

$option = new Option('-t|--time {TIME_STR}');
$context = new Context(['should_not_be_used']);
$parameters = $context->parameters;

$context->parameters->tentative('42');
$I->assertTrue($option->parse('t', $context, $parameters));
$I->assertEquals('42', $context->options()['t']);
$I->assertEquals('should_not_be_used', $context->parameters->next());

// ============================================
// see option works even if there is no more
// arguments

$option = new Option('-t|--time {TIME_STR}');
$context = new Context([]);
$parameters = $context->parameters;

$I->assertTrue($option->parse('time', $context, $parameters));

$value = $context->options()['time'];
$I->assertTrue(is_string($value));
$I->assertEquals('', $value);

// ============================================
// see the option works with option with value and pattern

$option = new Option('-t|--time {TIME_STR|\\d+}');
$context = new Context(['123abc']);
$parameters = $context->parameters;

$I->expectException(
    \Clim\Exception\OptionException::class,
    function () use ($option, $context, $parameters) {
        $option->parse('time', $context, $parameters);
    });

// ============================================
// detect invalid regular expression error

$option = new Option('-t {TIME_STR|[abc}'); // "[abc" is invalid regex
$context = new Context(['123']);
$parameters = $context->parameters;

$I->expectException(
    \Clim\Exception\DefinitionException::class,
    function () use ($option, $context, $parameters) {
        $option->parse('t', $context, $parameters);
    });

// ============================================
// see option default works

$option = (new Option('-t|--time {TIME_STR}'))
                ->default('today');
$context = new Context([]);

$option->collectDefaultValue($context);

$I->assertEquals([
    't' => 'today',
    'time' => 'today',
], $context->options());

