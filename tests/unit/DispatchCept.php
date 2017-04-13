<?php

$I = new UnitTester($scenario);
$I->wantTo('perform dispatch and see result');

$app = new Clim\App();

// global option
$app->option('--age {AGE|\\d+}');

$app->dispatch('{command}', [
    'foo' => function ($app) {
        // available only for foo
        $app->option('--force');

        $app->task(function ($context) {
            echo "FOO\n";
        });
    },
    'bar' => function ($app) {
        $app->task(function ($context) {
            echo "BAR\n";
        });
    },
]);

$output = $I->captureOutput(function () use($app) {
    $container = $app->getContainer();
    $container['argv'] = ['hello_dispatch', '--age=49', 'foo'];
    // try {
        $app->run();
    // } catch (\Exception $e) {

    // }
});

echo $output;
