<?php

namespace Clim;

use Clim\Cli\Parameters;
use Clim\Middleware\ContextInterface as MiddlewareContextInterface;

class Context implements MiddlewareContextInterface
{
    /** @var array */
    protected $_options;

    /** @var array $_arguments */
    protected $_arguments;

    /** @var App */
    protected $app;

    /**
     * hold result
     * @var string
     */
    protected $result;

    public function __construct()
    {
        $this->_options = [];
        $this->_arguments = [];
    }

    public function push($value, $name = null, $append = false)
    {
        array_push($this->_arguments, $value);

        if ($name) {
            if ($append) {
                if (isset($this->_arguments[$name])) {
                    $this->_arguments[] = $value;
                } else {
                    $this->_arguments[$name] = [$value];
                }
            } else {
                $this->_arguments[$name] = $value;
            }
        }
    }

    public function has($name)
    {
        return isset($this->_options[$name]);
    }

    public function set($name, $value)
    {
        $this->_options[$name] = $value;
    }

    public function options()
    {
        return $this->_options;
    }

    public function arguments()
    {
        return $this->_arguments;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function setApp(App $app)
    {
        $this->app = $app;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }
}
