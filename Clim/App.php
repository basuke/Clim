<?php

namespace Clim;

use Exception;
use Psr\Container\ContainerInterface;
use Throwable;

class App extends Builder
{
    /**
     * Constructor of Clim\App
     * @param ContainerInterface|array|null $container
     */
    public function __construct($container = null)
    {
        if (!($container instanceof \Psr\Container\ContainerInterface)) {
            $container = new Container((array) $container);
        }

        parent::__construct($container);
    }

    public function run()
    {
        $argv = $this->getContainer()->get('argv');
        $context = new Context(array_slice($argv, 1));

        try {
            $this->runner()->run($context);
        } catch (Exception $e) {
            $context = $this->handleException($e, $context);
        } catch (Throwable $e) {
            $context = $this->handlePhpError($e, $context);
        }
    }

    /**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just print error
     *
     * @param  Exception $e
     * @param  Context $context
     *
     * @return Context
     */
    protected function handleException(Exception $e, Context $context)
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
            $params = [$context, $e];
        // }

        if ($this->getContainer()->has($handler)) {
            $callable = $this->getContainer()->get($handler);
            // Call the registered handler
            return call_user_func_array($callable, $params);
        }

        // No handlers found, so just display simple error
        $this->displayErrorAndExit($e->getMessage(), $e->getCode() ?: 1);
    }

    /**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just re-throw.
     *
     * @param  Throwable $e
     * @param  Context $context
     * @return Context
     */
    protected function handlePhpError(Throwable $e, Context $context)
    {
        $handler = 'phpErrorHandler';
        $params = [$request, $response, $e];

        if ($this->getContainer()->has($handler)) {
            $callable = $this->getContainer()->get($handler);
            // Call the registered handler
            return call_user_func_array($callable, $params);
        }

        // No handlers found, so just display simple error
        $this->displayErrorAndExit($e->getMessage(), $e->getCode() ?: 1);
    }

    protected function displayErrorAndExit($message, $error = 1)
    {
        echo "Error occured. ". $message. "\n";
    }
}