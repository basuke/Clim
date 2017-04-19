<?php

use Clim\App;

$I = new UnitTester($scenario);
$I->wantTo('perform dispatch and see the result');

$app = new Clim\App(['name' => 'Kashiyuka']);

// global option
$app->option('--age {AGE|\\d+}');

$app->dispatch('{command}', [
    'foo' => function (App $app) {
        // available only for foo
        $app->option('--force');

        $app->task(function ($context) {
            // $context['age'] : from option
            // $this->name     : from container
            echo "FOO {$context['age']}-{$this->name}\n";
        });
    },
    'bar' => function (App $app) {
        $app->task(function ($context) {
            echo "BAR\n";
        });
    },
]);

$I->willPassArguments($app, ['hello_dispatch', '--age=49', 'foo']);
$I->assertOutputEquals($app, "FOO 49-Kashiyuka\n");

$I->willPassArguments($app, ['hello_dispatch', 'bar']);
$I->assertOutputEquals($app, "BAR\n");

