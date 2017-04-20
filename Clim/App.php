<?php

namespace Clim;

use Clim\Cli\Spec;
use Closure;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\DeferredCallable;
use Throwable;

class App
{
    /** @var ContainerInterface */
    private $container;

    /** @var Spec */
    private $spec;

    /**
     * Constructor of Clim\App
     * @param ContainerInterface|array|null $container
     */
    public function __construct($container = null)
    {
        if (!($container instanceof ContainerInterface)) {
            $container = new Container((array) $container);
        }

        $this->container = $container;

        $this->spec = new Spec();
    }

    /**
     * return application container
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $option
     * @param Closure|null $callable
     * @return Option
     */
    public function option($option, $callable = null)
    {
        $flags = 0;

        $option = new Option($option, $flags, $this->containerBoundCallable($callable));
        $this->spec->addOption($option);
        return $option;
    }

    /**
     * Alias of option()
     * @param string $option
     * @param Closure|null $callable
     * @return Option
     */
    public function opt($option, $callable = null)
    {
        return $this->option($option, $callable);
    }

    /**
     * @param string $name
     * @param Closure|null $callable
     * @return Argument
     */
    public function argument($name, $callable = null)
    {
        $flags = 0;

        $argument = new Argument($name, $flags, $this->containerBoundCallable($callable));
        $this->spec->addArgument($argument);
        return $argument;
    }

    /**
     * Alias of argument()
     * @param string $name
     * @param Closure|null $callable
     * @return Argument
     */
    public function arg($name, $callable = null)
    {
        return $this->argument($name, $callable);
    }

    /**
     * @param string $meta_var
     * @param array $children Definition of child apps
     * @return Dispatcher
     */
    public function dispatch($meta_var, $children)
    {
        $dispatcher = new Dispatcher($meta_var, $children, $this->getContainer());
        $this->spec->addArgument($dispatcher);
        return $dispatcher;
    }

    /**
     * Add middleware to application
     * @param callable $middleware
     * @return App
     *
     * @since 1.1.0
     */
    public function add($middleware)
    {
        $this->spec->pushMiddleware($this->containerBoundCallable($middleware));
        return $this;
    }

    /**
     * @param callable $callable
     */
    public function task($callable)
    {
        $this->spec->addTask($this->containerBoundCallable($callable));
    }

    /**
     * @return Runner
     */
    public function runner()
    {
        $runner = new Runner($this->spec);
        $runner->setApp($this);
        return $runner;
    }

    /**
     * @return Context|null
     */
    public function run()
    {
        try {
            /** @var array $argv */
            $argv = $this->getContainer()->get('argv');
            /** @@var Context $context */
            $context = $this->runner()->run($argv);
            return $context;
        } catch (Exception $e) {
            $this->handleException($e);
        } catch (Throwable $e) {
            $this->handlePhpError($e);
        }
        return null;
    }

    /**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just print error
     *
     * @param  Exception $e
     *
     * @return Context
     */
    protected function handleException(Exception $e)
    {
        // if ($e instanceof MethodNotAllowedException) {
        //     $handler = 'notAllowedHandler';
        //     $params = [$e->getRequest(), $e->getResponse(), $e->getAllowedMethods()];
        // } elseif ($e instanceof NotFoundException) {
        //     $handler = 'notFoundHandler';
        //     $params = [$e->getRequest(), $e->getResponse(), $e];
        // } elseif ($e instanceof SlimException) {
        //     // This is a Stop exception and contains the response
        //     return $e->getResponse();
        // } else {
            // Other exception, use $request and $response params
            $handler = 'errorHandler';
            $params = [$e];
        // }

        if ($this->getContainer()->has($handler)) {
            $callable = $this->getContainer()->get($handler);
            // Call the registered handler
            return call_user_func_array($callable, $params);
        }

        // No handlers found, so just display simple error
        $this->displayError($e->getMessage());
        exit($e->getCode() ?: 1);
    }

    /**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just re-throw.
     *
     * @param  Throwable $e
     * @return Context
     */
    protected function handlePhpError(Throwable $e)
    {
        $handler = 'phpErrorHandler';
        $params = [$e];

        if ($this->getContainer()->has($handler)) {
            $callable = $this->getContainer()->get($handler);
            // Call the registered handler
            return call_user_func_array($callable, $params);
        }

        // No handlers found, so just display simple error
        $this->displayError($e->getMessage());
        exit($e->getCode() ?: 1);
    }

    protected function displayError($message)
    {
        echo "Error occurred. ". $message. "\n";
    }

    protected function containerBoundCallable($callable)
    {
        if (is_null($callable)) return null;

        // if ($callable instanceof Closure) {
        //     $callable = $callable->bindTo($this->container);
        // }
        return new DeferredCallable($callable, $this->container);
    }
}