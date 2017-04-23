<?php

namespace Clim\Middleware;

use Clim\Context;
use Clim\Helper\Hash;
use Psr\Container\ContainerInterface;
use Symfony\Component\Debug\Debug;

/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/20/17
 * Time: 5:57 PM
 */

class DebugMiddleware
{
    private $display_error = true;
    private $error_reporting_level = E_ALL;

    public function __construct(ContainerInterface $c)
    {
        if (!class_exists('\Symfony\Component\Debug\Debug')) {
            throw new \Clim\Exception\DefinitionException("symfony/debug is required for " . static::class);
        }

        $this->display_error = isset($c['settings']['display_error']) ? $c['settings']['display_error'] : true;
        $this->error_reporting_level = isset($c['settings']['error_reporting_level']) ? $c['settings']['display_error'] : E_ALL;
    }

    public function __invoke(Context $context, callable $next)
    {
        $app = $context->getApp();

        $display_error = $this->display_error;
        $error_reporting_level = $this->error_reporting_level;

        $app->opt('-d|--debug', function () use (
            $app,
            $display_error,
            $error_reporting_level
        ) {
            $app->disableErrorHandling();

            Debug::enable($error_reporting_level, $display_error);
        });

        return $next($context);
    }
}