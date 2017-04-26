<?php

namespace Clim\Helper;

use Clim\Context;
use Closure;
use Psr\Container\ContainerInterface;

class ArgumentsSupplier
{
    protected $context;

    protected $container;

    protected $options;

    protected $arguments;

    protected $missingArgs;

    protected $assignedArguments;

    public function __construct(Context $context, ContainerInterface $container)
    {
        $this->context = $context;
        $this->container = $container;
        $this->options = new Hash($context->options());
        $this->arguments = new Hash($context->arguments());
    }

    public function supplyFor(Closure $closure)
    {
        $args = [];

        $ref = new \ReflectionFunction($closure);
        /** @var \ReflectionParameter[] $params */
        $params = $ref->getParameters();

        if (count($params) > 0) {
            $this->assignedArguments = [];
            $this->missingArgs = [];

            foreach ($params as $index => $param) {
                $class = $param->getClass();
                $allow_null = $param->allowsNull();

                $arg = $this->findArgumentFor(
                    $param->getName(),
                    $index,
                    $class ? $class->getName() : null,
                    $param->isArray()
                );

                $args[] = $arg;
            }

        }

        return $args;
    }

    protected function findArgumentFor($name, $index, $class, $is_array)
    {
        if ($name === 'arguments' || $name === 'args') {
            return $this->hashToDesiredArgument($this->arguments, $class, $is_array);
        }

        if ($name === 'options' || $name === 'opts') {
            return $this->hashToDesiredArgument($this->options, $class, $is_array);
        }

        if ($name === 'context') {
            return $this->context;
        }

        if ($this->container->has($name)) {
            $result = $this->container->get($name);
            if (!$class || ($result instanceof $class)) {
                return $result;
            }
        }

        if (isset($this->arguments[$name])) {
            $result = $this->arguments[$name];
            return $result;
        }

        if (isset($this->options[$name])) {
            $result = $this->options[$name];
            return $result;
        }

        foreach ($this->arguments->all() as $index => $value) {
            if (is_integer($index) && in_array($index, $this->assignedArguments, true) === false) {
                if (($is_array && is_array($value)) || ($class && $value instanceof $class)) {
                    $this->assignedArguments[] = $index;
                    return $value;
                }
            }
        }

        return null;
    }

    protected function hashToDesiredArgument(Hash $hash, $class, $is_array)
    {
        if ($is_array) {
            return $hash->all();
        } elseif ($class && $class !== 'Clim\\Helper\\Hash') {
            try {
                return new $class($hash->all());
            } catch (\Exception $e) {
                // ... pass through
            }
        }

        return $hash;
    }
}