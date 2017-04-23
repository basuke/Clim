<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/22/17
 * Time: 11:30 PM
 */

namespace Clim\Middleware;

use Clim\Context;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleMiddleware
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function __invoke(Context $context, callable $next)
    {
        $c = $this->container;

        $c["input"] = function ($c) {
            return new ArgvInput($c['argv']);
        };

        $c["output"] = function ($c) {
            $output =  new ConsoleOutput();
            return new SymfonyStyle($c["input"], $output);
        };

        return $next($context);
    }
}