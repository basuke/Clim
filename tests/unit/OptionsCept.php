<?php

use \Clim\Context;
use \Clim\OptionParser;

$I = new UnitTester($scenario);
$I->wantTo('define OptionParser features');

// ============================================
// see the parser matches with short option

$parser = new OptionParser('-f');
$I->assertTrue($parser->match('f'));

// ============================================
// see the parser matches with long option

$parser = new OptionParser('--foo');

$I->assertTrue($parser->match('foo'));

// ============================================
// see the parser matches with long option by
// mixing the definition

$parser = new OptionParser('-f|--foo');

$I->assertTrue($parser->match('f'));
$I->assertTrue($parser->match('foo'));
$I->assertFalse($parser->match('hello'));

// ============================================
// see the parser matches with option with value

$parser = new OptionParser('-k {VALUE}');

$I->assertTrue($parser->needValue());
$I->assertEquals('VALUE', $parser->metaVar());

// ============================================
// see the parser matches with mixed option with value

$parser = new OptionParser('-t|--time {TIME_STR}');

$I->assertTrue($parser->needValue());
$I->assertEquals('TIME_STR', $parser->metaVar());

// ============================================
// see the parser works with option with
// extra value

$parser = new OptionParser('-t|--time {TIME_STR}');
$context = new Context(['today', 'tomorrow']);

$I->assertTrue($parser->parse('t', $context));
$I->assertEquals('today', $context->options()['time']);
$I->assertEquals('tomorrow', $context->next());

// ============================================
// see the parser works with option with
// tentative value

$parser = new OptionParser('-t|--time {TIME_STR}');
$context = new Context(['should_not_be_used']);

$context->tentative('42');
$I->assertTrue($parser->parse('t', $context));
$I->assertEquals('42', $context->options()['t']);
$I->assertEquals('should_not_be_used', $context->next());

// ============================================
// see parser works even if there is no more
// arguments

$parser = new OptionParser('-t|--time {TIME_STR}');
$context = new Context([]);

$I->assertTrue($parser->parse('time', $context));

$value = $context->options()['time'];
$I->assertTrue(is_string($value));
$I->assertEquals('', $value);

// ============================================
// see the parser works with option with value and pattern

$parser = new OptionParser('-t|--time {TIME_STR|\\d+}');
$context = new Context(['123abc']);

$I->expectException(
    \Clim\Exception\OptionException::class,
    function () use ($parser, $context) {
        $parser->parse('time', $context);
    });

// ============================================
// detect invalid regular expression error

$parser = new OptionParser('-t {TIME_STR|[abc}'); // "[abc" is invalid regex

$I->expectException(
    \Clim\Exception\DefinitionException::class,
    function () use ($parser) {
        $parser->metaVar(); // causes definition parsing
    });

// ============================================
// see option default works

$parser = (new OptionParser('-t|--time {TIME_STR}'))
                ->default('today');
$context = new Context([]);

$parser->collectDefaultValue($context);

$I->assertEquals([
    't' => 'today',
    'time' => 'today',
], $context->options());

