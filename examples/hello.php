<?php

require dirname(__DIR__). '/vendor/autoload.php';

$app = new Clim\App();

$app->option('-u|--upper');

$app->task(function ($options, $arguments) {
	$name = $arguments[0] ?: 'someone';

	if ($options['u']) $name = strtoupper($name);

    echo "Welcome, {$name}\n";
});

$app->run();
