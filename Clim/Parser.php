<?php

namespace Clim;

use \ArrayIterator;
use \Closure;
use \Psr\Container\ContainerInterface;

class Parser
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

    public function parse(array $argv)
    {
        $arguments = $this->evaluateArguments(array_slice($argv, 1));
        $options = $this->collectOptions();

        foreach ($this->handlers as $handler) {
            $handler->reset();
        }

        return [$options, $arguments];
    }

    protected function evaluateArguments(array $argv)
    {
        $unused = new Collection();
        $arguments = new Collection($argv);

        while (!$arguments->isEmpty()) {
            /** @var string $arg */
            $arg = $arguments->shift();

            if ($arg == '--') break;

            if ($arg[0] != '-' || strlen($arg) <= 1) {
                $unused->push($arg);
            } elseif ($arg[1] != '-') {
                // short option
                $arg = substr($arg, 1);
                while ($arg) {
                    $option = $arg[0];
                    $arg = strlen($arg) > 1 ? substr($arg, 1) : null;

                    $this->handleOption($option, $arg, $arguments);
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

                $this->handleOption($option, $arg, $arguments);
            }
        }

        return $unused->append($arguments);
    }

    protected function handleOption($option, $value, Collection $arguments)
    {
        foreach ($this->handlers as /** @var Option $handler */ $handler) {
            if ($handler->evaluate($option, $value, $arguments)) return true;
        }

        return false;
    }

    protected function collectOptions()
    {
        $options = new Collection([]);

        foreach ($this->handlers as /** @var Option $handler */ $handler) {
            $handler->collect($options);
        }

        return $options;
    }
}