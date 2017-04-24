<?php

namespace Clim;

use Clim\Cli\ContextInterface;
use Clim\Helper\Hash;

class Context implements ContextInterface
{
    /** @var Hash*/
    protected $options;

    /** @var Hash */
    protected $arguments;

    /** @var App */
    protected $app;

    /**
     * @var mixed
     */
    protected $result;

    public function __construct()
    {
        $this->options = new Hash();
        $this->arguments = new Hash();
    }

    /**
     * @param mixed $value
     * @param string $name
     * @param bool $append
     */
    public function push($value, $name = null, $append = false)
    {
        $this->arguments->push($value);

        if ($name) {
            $this->arguments->set($name, $value, $append);
        }
    }

    /**
     * @param string $name key
     * @return bool
     */
    public function has($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * @param string $name key
     * @param mixed $value value
     * @param bool $append Default is false
     */
    public function set($name, $value, $append = false)
    {
        $this->options->set($name, $value, $append);
    }

    /**
     * @return array
     */
    public function options()
    {
        return $this->options->all();
    }

    /**
     * @return array
     */
    public function arguments()
    {
        return $this->arguments->all();
    }

    /**
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param App $app
     */
    public function setApp(App $app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @param callable $callable
     * @return mixed
     */
    public function execute(callable $callable)
    {
        $options = new Hash($this->options());
        $arguments = new Hash($this->arguments());

        $result = call_user_func_array($callable, [$options, $arguments]);
        if (!is_null($result)) $this->setResult($result);
        return $result;
    }
}
