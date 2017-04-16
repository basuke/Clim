<?php

namespace Clim;

use \Clim\Exception\OptionException;
use \Closure;
use \Psr\Container\ContainerInterface;

class Runner
{
    /** @var array */
    private $parsers = [];

    /** @var array */
    private $handlers = [];

    /** @var array */
    private $tasks = [];

    /**
     * @param OptionParser[] $parsers
     * @param ArgumentHandler[] $handlers
     */
    public function __construct(array $parsers, array $handlers, array $tasks = [])
    {
        $this->parsers = $parsers;
        $this->handlers = new Collection(array_slice($handlers, 0));
        $this->tasks = $tasks;
    }

    public function run($context)
    {
        if (is_array($context)) {
            $context = new Context($context);
        }

        $this->collectDefaultOptions($context);

        while ($context->hasMore()) {
            /** @var string $arg */
            $arg = $context->next();

            if ($arg == '--') break;

            if ($arg[0] != '-' || strlen($arg) <= 1) {
                $handled = $this->handleArgument($arg, $context);
            } elseif ($arg[1] != '-') {
                // short option
                $handled = $this->parseShortOption(substr($arg, 1), $context);
            } else {
                $handled = $this->parseLongOption(substr($arg, 2), $context);
            }
            if ($handled) return;
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($context->next(), $context);
        }

        foreach ($this->tasks as $task) {
            call_user_func(
                $task,
                new Collection($context->options()),
                new Collection($context->arguments())
            );
        }

        return $context;
    }

    protected function handleArgument($arg, Context $context)
    {
        /** @var ArgumentHandler */
        $handler = $this->handlers->shift();

        if ($handler) {
            return $handler->handle($arg, $context);
        } else {
            $context->push($arg);
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

        if (!is_null($context->tentative())) {
            throw new Exception\OptionException("value is not needed");
        }
    }

    protected function parse($option, Context $context)
    {
        foreach ($this->parsers as /** @var OptionParser */ $parser) {
            $parsed = $parser->parse($option, $context);
            if ($parsed) return;
        }

        throw new OptionException("unknown option");
    }

    protected function collectDefaultOptions(Context $context)
    {
        foreach ($this->parsers as $parser) {
            $parser->collectDefaultValue($context);
        }
    }
}