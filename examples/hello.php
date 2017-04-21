<?php

use Clim\App;
use Clim\Middleware\DebugMiddleware;

require dirname(__DIR__). '/vendor/autoload.php';

$app = new App();

$app->opt('-u|--upper');
$app->arg('name')->default('unknown');

$app->task(function ($opts, $args) {
    $name = $args['name'];

    if ($opts['u']) $name = strtoupper($name);

    echo "Welcome, {$name} {$unknown}\n"; // <-- this will invoke notice.
});

$app->add(DebugMiddleware::class);

$app->run();
