<?php

require dirname(__DIR__). '/vendor/autoload.php';

$container = new \Clim\Container([
    'quiet' => false
]);

$container['out'] = function($c) {
    return function($msg) use ($c) {
        if (!$c->quiet) echo $msg. "\n";
    };
};

$app = new Clim\App($container);

$app->option('-q|--quiet', function ($value) {
    $this['quiet'] = true;
});

$app->dispatch('{COMMAND}', [
    'hi' => function ($app) {
        $app->argument('name')
            ->default('world');

        $app->task(function ($opt, $args) {
            $out = $this->out;
            $out("Hi, {$args['name']}");
        });
    },

    'bye' => function ($app) {
        $app->option('-a|--again');

        $app->task(function ($opt, $args) {
            $msg = "Bye";
            if ($opt['again']) $msg .= " again";

            $out = $this->out;
            $out($msg);
        });
    },
]);

$app->run();
