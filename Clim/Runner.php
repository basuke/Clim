<?php

namespace Clim;

use \ArrayIterator;
use \Closure;
use \Psr\Container\ContainerInterface;

class Runner
{
    /** @var array $handlers */
    private $handlers = [];

    /**
     * @param OptionHandler[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function run(array $argv)
    {
        $context = new Context(array_slice($argv, 1));
        $this->parseArguments($context);
        return $context;
    }

    protected function parseArguments(Context $context)
    {
        while ($context->hasNext()) {
            /** @var string $arg */
            $arg = $context->next();

            if ($arg == '--') break;

            if ($arg[0] != '-' || strlen($arg) <= 1) {
                $context->push($arg);
            } elseif ($arg[1] != '-') {
                // short option
                $arg = substr($arg, 1);
                while ($arg) {
                    $option = $arg[0];
                    $arg = strlen($arg) > 1 ? substr($arg, 1) : null;

                    $this->handle($option, $arg, $context);
                }
            } else {
                // long option
                $pos = strpos($arg, '=');
                if ($pos > 2) {
                    $option = substr($arg, 2, $pos - 2);
                    $arg = substr($arg, $pos + 1);
                } else {
                    $option = substr($arg, 2);
                    $arg = null;
                }

                $this->handle($option, $arg, $context);
            }
        }
    }

    protected function handle($option, $value, Context $context)
    {
        foreach ($this->handlers as /** @var Option $handler */ $handler) {
            if ($handler->handle($option, $value, $context)) return true;
        }

        return false;
    }
}