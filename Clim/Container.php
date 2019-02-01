<?php

namespace Clim;

use Clim\Helper\Hash;
use Clim\Middleware\ConsoleMiddleware;
use Clim\Middleware\DatabaseMiddleware;
use Clim\Middleware\DebugMiddleware;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Pimple\Container as PimpleContainer;
use Slim\CallableResolver;
use Slim\Exception\ContainerValueNotFoundException;
use Slim\Exception\ContainerException as SlimContainerException;

class Container extends PimpleContainer implements ContainerInterface
{
    /**
     * Create new container
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $userSettings = isset($values['settings']) ? $values['settings'] : [];

        $defaultSettings = [
            'displayErrorDetails' => false,
        ];

        /**
         * This service MUST return an array or an
         * instance of \ArrayAccess.
         *
         * @return array|\ArrayAccess
         */
        $this['settings'] = function () use ($userSettings, $defaultSettings) {
            return Hash::merge($defaultSettings, $userSettings);
        };

        self::registerDefaultServices($this);
    }

    /**
     * This function registers the default services that Clim needs to work.
     *
     * All services are shared - that is, they are registered such that the
     * same instance is returned on subsequent calls.
     */
    public static function registerDefaultServices(ContainerInterface $container) : void
    {
        if (!$container->has('argv')) {
            $container['argv'] = $_SERVER['argv'];
        }

        if (!$container->has('Debug')) {
            $container['Debug'] = function (ContainerInterface $c) {
                return new DebugMiddleware($c);
            };
        }

        if (!$container->has('Database')) {
            $container['Database'] = function (ContainerInterface $c) {
                return new DatabaseMiddleware($c);
            };
        }

        if (!$container->has('Console')) {
            $container['Console'] = function (ContainerInterface $c) {
                return new ConsoleMiddleware($c);
            };
        }

        if (!$container->has('callableResolver')) {
            /**
             * Instance of \Slim\Interfaces\CallableResolverInterface
             *
             * @param ContainerInterface $c
             *
             * @return CallableResolver
             */
            $container['callableResolver'] = function (ContainerInterface $c) {
                return new CallableResolver($c);
            };
        }
    }

    /********************************************************************************
     * Methods to satisfy Interop\Container\ContainerInterface
     *******************************************************************************/

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerValueNotFoundException  No entry was found for this identifier.
     * @throws ContainerException               Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->offsetExists($id)) {
            throw new ContainerValueNotFoundException(sprintf('Identifier "%s" is not defined.', $id));
        }
        try {
            return $this->offsetGet($id);
        } catch (\InvalidArgumentException $exception) {
            if ($this->exceptionThrownByContainer($exception)) {
                throw new SlimContainerException(
                    sprintf('Container error while retrieving "%s"', $id),
                    null,
                    $exception
                );
            } else {
                throw $exception;
            }
        }
    }

    /**
     * Tests whether an exception needs to be recast for compliance with Container-Interop.  This will be if the
     * exception was thrown by Pimple.
     *
     * @param \InvalidArgumentException $exception
     *
     * @return bool
     */
    private function exceptionThrownByContainer(\InvalidArgumentException $exception)
    {
        $trace = $exception->getTrace()[0];

        return $trace['class'] === PimpleContainer::class && $trace['function'] === 'offsetGet';
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }
}
