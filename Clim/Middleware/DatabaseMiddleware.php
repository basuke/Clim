<?php

namespace Clim\Middleware;

use Clim\Context;
use Clim\Helper\Hash;
use Psr\Container\ContainerInterface;

class DatabaseMiddleware
{
    private $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function __invoke(Context $context, callable $next)
    {
        $app = $context->getApp();

        $app->opt('--dsn {DSN}');

        $this->container['db'] = function($c) use ($context) {
            $config = Hash::merge(
                [
                    'dsn' => null,
                    'fetch_mode' => \PDO::FETCH_ASSOC,
                ],
                $c['settings']['database'],
                Hash::subset($context->options(), ['dsn'])
            );

            $db = new \PDO($config['dsn']);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $config['fetch_mode']);

            return $db;
        };

        return $next($context);
    }
}