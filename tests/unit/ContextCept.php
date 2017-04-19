<?php

use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo("define Context features");

$context = new Context(['Hello', 'world']);
$parameters = $context->parameters;
$I->assertEquals($parameters->argv(), ['Hello', 'world']);
$I->assertEquals($parameters->next(), 'Hello');
$I->assertTrue($parameters->hasMore());
$I->assertEquals($parameters->next(), 'world');
$I->assertFalse($parameters->hasMore());
$I->assertEquals($parameters->next(), null);

$context->push('foo');
$context->push('bar');
$I->assertEquals($context->arguments(), ['foo', 'bar']);

$context->set('foo', 'bar');
$context->set('f', 'bar');
$I->assertEquals($context->options(), ['foo' => 'bar', 'f' => 'bar']);


$context = new Context(['hello', 'world']);
$context->push('foo');
$I->assertEquals($context->arguments(), ['foo', 'hello', 'world']);

$I->assertTrue(is_null($parameters->tentative()));
$parameters->tentative('hi');
$I->assertEquals('hi', $parameters->tentative());
$I->assertTrue(is_null($parameters->tentative()));

