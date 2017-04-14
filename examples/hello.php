<?php

require dirname(__DIR__). '/vendor/autoload.php';

$app = new Clim\App();

$app->opt('-u|--upper');
$app->arg('name')->default('unknown');

$app->task(function ($opts, $args) {
    $name = $args['name'];

    if ($opts['u']) $name = strtoupper($name);

    echo "Welcome, {$name}\n";
});

$app->run();
