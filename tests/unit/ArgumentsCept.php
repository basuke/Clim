<?php

use Clim\ArgumentHandler;
use Clim\Context;

$I = new UnitTester($scenario);
$I->wantTo('define how ArgumentParser works');

$handler = new ArgumentHandler('arg1');

$context = new Context();

$handler->handle('hello', $context);

$I->assertEquals($handler->metaVar(), 'arg1');
$I->assertEquals($context->arguments(), [
    0 => 'hello',
    'arg1' => 'hello'
]);
