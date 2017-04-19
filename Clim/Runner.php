<?php

namespace Clim;

use Clim\Cli\ArgumentInterface;
use Clim\Cli\Parameters;
use Clim\Cli\Spec;
use Clim\Exception\OptionException;
use Clim\Middleware\MiddlewareStack;
use Slim\Collection;

class Runner
{
    /** @var App */
    protected $app;

    /** @var Spec */
    private $spec;

    /** @var Parameters */
    protected $parameters;

    /** @var array */
    private $running_arguments;

    /**
     * @param Spec $spec
     */
    public function __construct(Spec $spec)
    {
        $this->spec = $spec;
    }

    public function setApp(App $app)
    {
        $this->app = $app;
    }

    public function run($context)
    {
        if (is_array($context)) {
            $context = new Context($context);
        }

        $this->parameters = $context->parameters;

        if ($this->app) $context->setApp($this->app);

        return $this->spec->taskMiddleware()->run($context, function (Context $context) {
            if ($this->parse($context)) return $context;

            $options = new Collection($context->options());
            $arguments = new Collection($context->arguments());

            foreach ($this->spec->tasks() as $task) {
                call_user_func($task, $options, $arguments);
            }

            return $context;
        });
    }

    protected function parse(Context $context)
    {
        $this->running_arguments = array_slice($this->spec->arguments(), 0);

        $this->collectDefaultOptions($context);

        while ($this->parameters->hasMore()) {
            /** @var string $arg */
            $arg = $this->parameters->next();

            if ($arg == '--') break;

            if ($arg[0] != '-' || strlen($arg) <= 1) {
                $handled = $this->handleArgument($arg, $context);
                if ($handled) return true;
            } elseif ($arg[1] != '-') {
                // short option
                $this->parseShortOption(substr($arg, 1), $context);
            } else {
                $this->parseLongOption(substr($arg, 2), $context);
            }
        }

        foreach ($this->running_arguments as $argument) {
            $argument->handle($this->parameters->next(), $context);
        }

        return false;
    }

    protected function handleArgument($arg, Context $context)
    {
        /** @var ArgumentInterface */
        $argument = array_shift($this->running_arguments);

        if ($argument) {
            return $argument->handle($arg, $context);
        } else {
            $context->push($arg);
            return false;
        }
    }

    protected function parseShortOption($arg, Context $context)
    {
        while ($arg) {
            $value = $arg[0];
            $arg = strlen($arg) > 1 ? substr($arg, 1) : null;

            $this->parameters->tentative($arg);
            $this->parseOption($value, $context);
            $arg = $this->parameters->tentative();
        }
    }

    protected function parseLongOption($value, Context $context)
    {
        // long option
        $pos = strpos($value, '=');

        if ($pos !== false) {
            $this->parameters->tentative(substr($value, $pos + 1));
            $value = substr($value, 0, $pos);
        }

        $this->parseOption($value, $context);

        if (!is_null($this->parameters->tentative())) {
            throw new Exception\OptionException("value is not needed");
        }
    }

    protected function parseOption($value, Context $context)
    {
        foreach ($this->spec->options() as /** @var Option */ $option) {
            $parsed = $option->parse($value, $context, $this->parameters);
            if ($parsed) return;
        }

        throw new OptionException("unknown option");
    }

    protected function collectDefaultOptions(Context $context)
    {
        foreach ($this->spec->options() as $option) {
            $option->collectDefaultValue($context);
        }
    }
}