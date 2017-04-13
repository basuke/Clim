<?php

use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo("define Context features");

$context = new Context(['Hello', 'world']);
$I->assertEquals($context->argv(), ['Hello', 'world']);
$I->assertEquals($context->next(), 'Hello');
$I->assertTrue($context->hasNext());
$I->assertEquals($context->next(), 'world');
$I->assertFalse($context->hasNext());
$I->assertEquals($context->current(), 'world');
$I->assertEquals($context->next(), null);

$context->push('foo');
$context->push('bar');
$I->assertEquals($context->arguments(), ['foo', 'bar']);

$context['foo'] = 'bar';
$context['f'] = 'bar';
$I->assertEquals($context->options(), ['foo' => 'bar', 'f' => 'bar']);


$context = new Context(['hello', 'world']);
$context->push('foo');
$I->assertEquals($context->arguments(), ['foo', 'hello', 'world']);

$context->unshift('xxx');
$context->push('yyy');
$I->assertEquals($context->arguments(), ['foo', 'yyy', 'xxx', 'hello', 'world']);

$I->assertTrue(is_null($context->tentative()));
$context->tentative('hi');
$I->assertEquals($context->tentative(), 'hi');
$I->assertTrue(is_null($context->tentative()));

