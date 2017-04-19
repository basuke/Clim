<?php
/**
 * Created by PhpStorm.
 * Date: 4/19/17
 * Time: 12:53 PM
 */

namespace Clim\Cli;


class Parameters
{
    /** @var array */
    protected $_argv;

    /** @var string hold tentative value */
    protected $_tentative;

    public function __construct(array $argv)
    {
        $this->_argv = $argv;
    }

    public function argv()
    {
        return $this->_argv;
    }

    public function next()
    {
        $current = array_shift($this->_argv);
        return $current;
    }

    public function hasMore()
    {
        return count($this->_argv) > 0;
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
            return null;
        }
    }
}