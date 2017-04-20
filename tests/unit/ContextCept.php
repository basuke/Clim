<?php

use Clim\Cli\Parameters;
use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo("define Context features");

$parameters = new Parameters(['Hello', 'world']);
$I->assertEquals($parameters->next(), 'Hello');
$I->assertTrue($parameters->hasMore());
$I->assertEquals($parameters->next(), 'world');
$I->assertFalse($parameters->hasMore());
$I->assertEquals($parameters->next(), null);

$parameters = new Parameters(['Hello', 'world']);
$I->assertEquals($parameters->dumpOut(), ['Hello', 'world']);
$I->assertFalse($parameters->hasMore());

$context = new Context();
$context->push('foo');
$context->push('bar');
$I->assertEquals($context->arguments(), ['foo', 'bar']);

$context->set('foo', 'bar');
$context->set('f', 'bar');
$I->assertEquals($context->options(), ['foo' => 'bar', 'f' => 'bar']);

// ==============================

$parameters = new Parameters(['hello', 'world']);

$I->assertTrue(is_null($parameters->tentative()));
$parameters->tentative('hi');
$I->assertEquals('hi', $parameters->tentative());
$I->assertTrue(is_null($parameters->tentative()));

