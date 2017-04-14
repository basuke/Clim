<?php

$I = new UnitTester($scenario);
$I->wantTo('perform dispatch and see the result');

$app = new Clim\App();

// global option
$app->option('--age {AGE|\\d+}');

$app->dispatch('{command}', [
    'foo' => function ($app) {
        // available only for foo
        $app->option('--force');

        $app->task(function ($context) {
            echo "FOO{$context['age']}\n";
        });
    },
    'bar' => function ($app) {
        $app->task(function ($context) {
            echo "BAR\n";
        });
    },
]);

$I->willPassArguments($app, ['hello_dispatch', '--age=49', 'foo']);
$I->assertOutputEquals($app, "FOO49\n");

$I->willPassArguments($app, ['hello_dispatch', 'bar']);
$I->assertOutputEquals($app, "BAR\n");

