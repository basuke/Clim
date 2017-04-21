<?php

use Clim\Cli\Parameters;
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
$parameters = new Parameters(['today', 'tomorrow']);
$context = new Context();

$I->assertTrue($option->parse('t', $parameters, $context));
$I->assertEquals('today', $context->options()['time']);
$I->assertEquals('tomorrow', $parameters->next());

// ============================================
// see the option works with option with
// tentative value

$option = new Option('-t|--time {TIME_STR}');
$parameters = new Parameters(['should_not_be_used']);
$context = new Context();

$parameters->tentative('42');
$I->assertTrue($option->parse('t', $parameters, $context));
$I->assertEquals('42', $context->options()['t']);
$I->assertEquals('should_not_be_used', $parameters->next());

// ============================================
// see option works even if there is no more
// arguments

$option = new Option('-t|--time {TIME_STR}');
$parameters = new Parameters([]);
$context = new Context();

$I->assertTrue($option->parse('time', $parameters, $context));

$value = $context->options()['time'];
$I->assertTrue(is_string($value));
$I->assertEquals('', $value);

// ============================================
// see the option works with option with value and pattern

$option = new Option('-t|--time {TIME_STR|\\d+}');
$parameters = new Parameters(['123abc']);
$context = new Context();

$I->expectException(
    \Clim\Exception\OptionException::class,
    function () use ($option, $context, $parameters) {
        $option->parse('time', $parameters, $context);
    });

// ============================================
// detect invalid regular expression error

$option = new Option('-t {TIME_STR|[abc}'); // "[abc" is invalid regex
$parameters = new Parameters(['123']);
$context = new Context();

$I->expectException(
    \Clim\Exception\DefinitionException::class,
    function () use ($option, $context, $parameters) {
        $option->parse('t', $parameters, $context);
    });

// ============================================
// see option default works

$option = new Option('-t|--time {TIME_STR}');
$option->default('today');
$context = new Context([]);

$option->collectDefaultValue($context);

$I->assertEquals([
    't' => 'today',
    'time' => 'today',
], $context->options());

// ============================================
// test multiple option with value

$option = new Option('--email {EMAIL}');
$option->multiple();

$parameters = new Parameters([]);
$context = new Context();

$parameters->tentative('foo@example.com');
$option->parse('email', $parameters, $context);

$parameters->tentative('bar@example.com');
$option->parse('email', $parameters, $context);

$value = $context->options()['email'];
$I->assertEquals([
    'foo@example.com',
    'bar@example.com',
], $value);

// ============================================
$I->wantTo("test options with callback");

$option = new Option("-d", 0, function (Context $context) use ($I) {
    $I->assertFalse(isset($context->options()['d']));
    $context->set('called', true);
});

$parameters = new Parameters([]);
$context = new Context();
$option->parse('d', $parameters, $context);

$I->assertTrue($context->options()['called']);
$I->assertFalse(isset($context->options()['d']));

// ============================================
$I->wantTo("test options with callback which return value");

$option = new Option("-d", 0, function (Context $context) use ($I) {
    return false;
});

$parameters = new Parameters([]);
$context = new Context();
$option->parse('d', $parameters, $context);

$I->assertEquals(false, $context->options()['d']);

// ============================================
$I->wantTo("test valued options with callback");

$option = new Option("-d {VAL}", 0, function (Context $context, $value) use ($I) {
    return strtoupper($value);
});

$parameters = new Parameters(['hi-ho']);
$context = new Context();
$option->parse('d', $parameters, $context);

$I->assertEquals('HI-HO', $context->options()['d']);

