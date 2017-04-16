<?php

namespace Clim;

class Container extends \Slim\Container
{
    /**
     * This function registers the default services that Slim needs to work.
     *
     * All services are shared - that is, they are registered such that the
     * same instance is returned on subsequent calls.
     *
     * @param array $userSettings Associative array of application settings
     *
     * @return void
     */
    protected function registerDefaultServices($userSettings)
    {
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
            return new Collection(array_merge($defaultSettings, $userSettings));
        };

        if (!$this->has('argv')) {
            $this['argv'] = $_SERVER['argv'];
        }

        if (!$this->has('callableResolver')) {
            /**
             * Instance of \Slim\Interfaces\CallableResolverInterface
             *
             * @param Container $this
             *
             * @return CallableResolverInterface
             */
            $this['callableResolver'] = function ($container) {
                return new \Slim\CallableResolver($container);
            };
        }
    }
}