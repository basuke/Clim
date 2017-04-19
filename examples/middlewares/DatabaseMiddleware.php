<?php

use Psr\Container\ContainerInterface;

class DatabaseMiddleware
{
    private $container;

    public function __construct(ContainerInterface $c)
    {

        $this->container = $c;
    }

    public function __invoke($context, $next)
    {
        $app = $context->getApp();

        $app->opt('--dsn {DSN}');

        $this->container['db'] = function($c) use ($context) {
            $opt = $context->options();
            $dsn = $opt['dsn'] ?: $c['settings']['dsn'];
            echo $dsn . "\n";

            $db = new PDO($dsn);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $db;
        };
        return $next($context);
    }
}