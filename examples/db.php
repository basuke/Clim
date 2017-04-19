<?php

require dirname(__DIR__). '/vendor/autoload.php';
require __DIR__. '/middlewares/DatabaseMiddleware.php';

$app = new Clim\App(['settings' => [
    // default database connection parameter.
    // it can be overwritten by '--dsn' option from cli.
    // @see middlewares/DatabaseMiddleware.php

    'dsn' => 'pgsql:host=localhost;dbname=sampledb;user=foobar',
]]);

$app->add('DatabaseMiddleware');
$app->arg('sql');

$app->task(function ($opts, $args) {
    // database connection is supplied by service provier
    $db = $this->db;

    // what to execute is from command line argument
    $sql = $args['sql'];

    echo $sql . "\n";

    foreach ($db->query($sql) as $row) {
        foreach ($row as $key => $value) {
            echo "{$key} = $value ";
        }
        echo "\n";
    }
});

$app->run();
