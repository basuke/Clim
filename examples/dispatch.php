<?php

use Clim\App;
use Clim\Context;

require dirname(__DIR__). '/vendor/autoload.php';

$container = new \Clim\Container([
    'count' => 1
]);

$container['out'] = function($c) {
    return function($msg) use ($c) {
        for ($i = 0; $i < $c->count; $i++) {
            echo $msg. "\n";
        }
    };
};

$app = new Clim\App($container);

$app->option('-c|--count {COUNT}', function (Context $context, $value) {
    $this['count'] = $value;
});

$app->dispatch('{COMMAND}', [
    'hi' => function (App $app) {
        $app->argument('name')
            ->default('world');

        $app->task(function ($opt, $args) {
            $out = $this->out;
            $out("Hi, {$args['name']}");
        });
    },

    'bye' => function (App $app) {
        $app->option('-a|--again');

        $app->task(function ($opt, $args) {
            $msg = "Bye";
            if ($opt['again']) $msg .= " again";

            $out = $this->out;
            $out($msg);
        });
    },
]);

$app->add('Console');
$app->add('Debug');

$app->run();
