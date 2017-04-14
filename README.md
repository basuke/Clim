# Clim Framework
PHP Micro framework for command line application inspired by Slim Framework

[![Build Status](https://travis-ci.org/climphp/Clim.svg?branch=master)](https://travis-ci.org/climphp/Clim)

## Features

- Container support
- Easy yet powerful configuration
- Command dispatch

## Usage

After `composer install`, create an `hello.php` file with the following contents:

```php
<?php

require 'vendor/autoload.php';

$app = new Clim\App();

$app->opt('-u|--upper');
$app->arg('name')->default('unknown');

$app->task(function ($opts, $args) {
	$name = $args['name'];

	if ($opts['u']) $name = strtoupper($name);

    echo "Welcome, {$name}\n";
});

$app->run();
```

Then from the shell:

```bash
$ php hello.php Nocchi
Welcome, Nocchi
$ php hello.php -u Kashiyuka
Welcome, KASHIYUKA
```

For more information on how to configure your clie application, see the [Documentation](https://www.climframework.com/docs/start/cli.html).

## Tests

To execute the test suite, you'll need codeception. 

```bash
$ vendor/bin/codecept run unit
```

## Credits

- [Basuke Suzuki](https://github.com/basuke)

Special thanks to [Slim Framework](https://www.slimframework.com/) for the archtecture and the api design.

## License

The Clim Framework is licensed under the MIT license. See [License File](LICENSE) for more information.
