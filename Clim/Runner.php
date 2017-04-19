<?php

namespace Clim;

use Clim\Cli\ArgumentInterface;
use Clim\Exception\OptionException;
use Clim\Middleware\MiddlewareStack;
use Closure;
use Psr\Container\ContainerInterface;
use Slim\Collection;

class Runner
{
    /** @var array */
    private $options = [];

    /** @var array */
    private $arguments = [];

    /** @var array */
    private $tasks = [];

    /** @var MiddlewareStack */
    private $task_middleware;

    /** @var array */
    private $running_arguments;

    /**
     * @param Option[] $options
     * @param ArgumentInterface[] $arguments
     */
    public function __construct(array $options = [], array $arguments = [])
    {
        $this->task_middleware = new MiddlewareStack();

        $this->options = $options;
        $this->arguments = $arguments;
    }

    public function addOption(Option $option)
    {
        $this->options[] = $option;
    }

    public function addArgument(ArgumentInterface $argument)
    {
        $this->arguments[] = $argument;
    }

    public function addTask(callable $task)
    {
        $this->tasks[] = $task;
    }

    public function pushMiddleware(callable $middleware)
    {
        $this->task_middleware->push($middleware);
    }

    public function run($context)
    {
        if (is_array($context)) {
            $context = new Context($context);
        }

        $this->running_arguments = array_slice($this->arguments, 0);

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

        foreach ($this->running_arguments as $argument) {
            $argument->handle($context->next(), $context);
        }

        return $this->task_middleware->run($context, function ($context) {
            $options = new Collection($context->options());
            $arguments = new Collection($context->arguments());

            foreach ($this->tasks as $task) {
                call_user_func($task, $options, $arguments);
            }

            return $context;
        });
    }

    protected function handleArgument($arg, Context $context)
    {
        /** @var ArgumentInterface */
        $argument = array_shift($this->running_arguments);

        if ($argument) {
            return $argument->handle($arg, $context);
        } else {
            $context->push($arg);
        }
    }

    protected function parseShortOption($arg, Context $context)
    {
        while ($arg) {
            $value = $arg[0];
            $arg = strlen($arg) > 1 ? substr($arg, 1) : null;

            $context->tentative($arg);
            $this->parse($value, $context);
            $arg = $context->tentative();
        }
    }

    protected function parseLongOption($value, Context $context)
    {
        // long option
        $pos = strpos($value, '=');

        if ($pos !== false) {
            $context->tentative(substr($value, $pos + 1));
            $value = substr($value, 0, $pos);
        }

        $this->parse($value, $context);

        if (!is_null($context->tentative())) {
            throw new Exception\OptionException("value is not needed");
        }
    }

    protected function parse($value, Context $context)
    {
        foreach ($this->options as /** @var Option */ $option) {
            $parsed = $option->parse($value, $context);
            if ($parsed) return;
        }

        throw new OptionException("unknown option");
    }

    protected function collectDefaultOptions(Context $context)
    {
        foreach ($this->options as $option) {
            $option->collectDefaultValue($context);
        }
    }
}