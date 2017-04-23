<?php

use Symfony\Component\Debug\Debug;

require dirname(__DIR__). '/vendor/autoload.php';

$app = new Clim\App(['settings' => [
    // default database connection parameter.
    // it can be overwritten by '--dsn' option from cli.
    // @see \Clim\Middleware/DatabaseMiddleware.php
    'database' => [
        'dsn' => 'pgsql:host=localhost;dbname=sampledb;user=foobar',
    ],
]]);

$app->arg('sql');

$app->task(function ($opts, $args) {
    // database connection is supplied by service provier
    /** @var \PDO $db */
    $db = $this->db;

    /** @var \Symfony\Component\Console\Style\SymfonyStyle $output */
    $output = $this->output;

    // what to execute is from command line argument
    $sql = $args['sql'];

    $output->title($sql);

    foreach ($db->query($sql) as $row) {
        foreach ($row as $key => $value) {
            echo "{$key} = $value ";
        }
        echo "\n";
    }
});

$app->add('Database');
$app->add('Console');
$app->add('Debug');

$app->run();
