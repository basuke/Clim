<?php

namespace Clim\Cli;

use RuntimeException;
use UnexpectedValueException;

/**
 * MiddlewareStack
 *
 * This is a class that enables concentric middleware layers.
 */
class MiddlewareStack
{
    /**
     * Middleware call stack
     *
     * @var  callable
     */
    protected $head = null;

    /**
     * Middleware core task
     *
     * @var callable
     */
    protected $kernel;

    /**
     * Add middleware
     *
     * This method pushs new middleware to themiddleware stack.
     *
     * @param callable $callable Any callable that accepts two arguments:
     *                           1. A ContextInterface object
     *                           2. A "next" middleware callable
     *
     * @throws RuntimeException         If middleware is added while the stack is dequeuing
     * @throws UnexpectedValueException If the middleware doesn't return a ContextInterface
     */
    public function push(callable $callable)
    {
        if ($this->kernel) {
            throw new RuntimeException('Middleware can’t be added once the stack is dequeuing');
        }

        if (is_null($this->head)) {
            $next = function (ContextInterface $context) {
                $result = call_user_func($this->kernel, $context);
                return $this->handleResult($context, $result);
            };
        } else {
            $next = $this->head;
        }

        $this->head = function (ContextInterface $context) use (
            $callable,
            $next
        ) {
            $result = call_user_func($callable, $context, $next);
            return $this->handleResult($context, $result);
        };
    }

    /**
     * Call middleware stack
     *
     * @param  ContextInterface $context A context object
     * @param  callable $kernel A core of middleware task
     *
     * @return ContextInterface
     */
    public function run(ContextInterface $context, callable $kernel)
    {
        if (empty($this->head)) {
            return $this->handleResult($context, call_user_func($kernel, $context));
        }

        $this->kernel = $kernel;

        /** @var callable $start */
        $start = $this->head;
        $context = $start($context);

        $this->kernel = null;

        return $context;
    }

    /**
     * Validate middleware result. Imprementer shall invoke
     * exceptions in this method if result is not valid.
     *
     * @param  mixed $result
     *
     * @return ContextInterface|null
     */
    protected function validateResult($result)
    {
        return null;
    }

    /**
     * Validate middleware result. Imprementer shall invoke
     * exceptions in this method if result is not valid.
     *
     * @param  ContextInterface $context A context object
     * @param  mixed $result
     * @return ContextInterface|null
     */
    protected function handleResult(ContextInterface $context, $result)
    {
        $validated = $this->validateResult($result);
        if (is_null($validated)) {
            $validated = $result;
        }

        if ($validated instanceof ContextInterface) {
            $context = $validated;
        } elseif (!is_null($validated)) {
            $context->setResult($validated);
        }

        return $context;
    }
}
