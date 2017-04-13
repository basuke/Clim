<?php

namespace Clim;

use \Clim\Exception\OptionException;
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
                $this->parseShortOption(substr($arg, 1), $context);
            } else {
                $this->parseLongOption(substr($arg, 2), $context);
            }
        }
    }

    protected function parseShortOption($arg, Context $context)
    {
        while ($arg) {
            $option = $arg[0];
            $arg = strlen($arg) > 1 ? substr($arg, 1) : null;

            $context->tentative($arg);
            $this->handle($option, $context);
            $arg = $context->tentative();
        }
    }

    protected function parseLongOption($option, Context $context)
    {
        // long option
        $pos = strpos($option, '=');

        if ($pos !== false) {
            $context->tentative(substr($option, $pos + 1));
            $option = substr($option, 0, $pos);
        }

        $this->handle($option, $context);
    }

    protected function handle($option, Context $context)
    {
        foreach ($this->handlers as /** @var OptionHandler */ $handler) {
            if ($handler->handle($option, $context)) return;
        }

        throw new OptionException();
    }
}