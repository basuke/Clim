<?php

namespace Clim;

class Context implements \ArrayAccess
{
    /** @var array $_argv */
    protected $_argv;

    /** @var string $_current */
    protected $_current;

    /** @var Collection $_options */
    protected $_options;

    /** @var array $_arguments */
    protected $_arguments;

    /** @var string hold tentative value */
    protected $_tentative;

    public function __construct(array $argv)
    {
        $this->_argv = $argv;
        $this->_options = new Collection();
        $this->_arguments = [];
    }

    public function argv()
    {
        return array_slice($this->_argv, 0);
    }

    public function next()
    {
        $this->_current = array_shift($this->_argv);
        return $this->_current;
    }

    public function hasNext()
    {
        return count($this->_argv) > 0;
    }

    public function current()
    {
        return $this->_current;
    }

    public function push($value)
    {
        array_push($this->_arguments, $value);
    }

    public function unshift($value)
    {
        array_unshift($this->_argv, $value);
    }

    public function options()
    {
        return $this->_options->all();
    }

    public function arguments()
    {
        return array_merge($this->_arguments, $this->_argv);
    }

    public function tentative($value = null)
    {
        if (is_null($value)) {
            if (!is_null($this->_tentative)) {
                $value = $this->_tentative;
                $this->_tentative = null;
            }
            return $value;
        } else {
            $this->_tentative = $value;
        }
    }

    protected function target($index)
    {
        return is_int($index) ? $this->_arguments : $this->_options;
    }

    public function offsetExists($index)
    {
        $array = $this->target($index);
        return isset($array[$index]);
    }

    public function offsetGet($index)
    {
        $array = $this->target($index);
        return $array[$index];
    }

    public function offsetSet($index, $value)
    {
        $array = $this->target($index);
        $array[$index] = $value;
    }

    public function offsetUnset($index)
    {
        $array = $this->target($index);
        unset($array[$index]);
    }
}
