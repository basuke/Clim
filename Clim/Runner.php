<?php

namespace Clim;

use \Clim\Exception\OptionException;
use \Closure;
use \Psr\Container\ContainerInterface;

class Runner
{
    /** @var array $parsers */
    private $parsers = [];

    /** @var array $handlers */
    private $handlers = [];

    /**
     * @param OptionParser[] $parsers
     * @param ArgumentHandler[] $handlers
     */
    public function __construct(array $parsers, array $handlers)
    {
        $this->parsers = $parsers;
        $this->handlers = new Collection($handlers);
    }

    public function run(array $argv)
    {
        $context = new Context(array_slice($argv, 1));
        $this->runWithContext($context);
        return $context;
    }

    protected function runWithContext(Context $context)
    {
        while ($context->hasNext()) {
            /** @var string $arg */
            $arg = $context->next();

            if ($arg == '--') break;

            if ($arg[0] != '-' || strlen($arg) <= 1) {
                /** @var ArgumentHandler */
                $handler = $this->handlers->shift();

                if ($handler) {
                    $handled = $handler->handle($arg, $context);
                    if ($handled) return;
                } else {
                    $context->push($arg);
                }
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
            $this->parse($option, $context);
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

        $this->parse($option, $context);
    }

    protected function parse($option, Context $context)
    {
        foreach ($this->parsers as /** @var OptionParser */ $parser) {
            $parsed = $parser->parse($option, $context);
            if ($parsed) return;
        }

        throw new OptionException();
    }
}