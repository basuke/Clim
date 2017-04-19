<?php

namespace Clim\Cli;

use Clim\Helper\MethodAliasTrait;

class Component
{
    use MethodAliasTrait;

    /** @var string */
    protected $description = '';

    /** @var mixed value */
    protected $default = null;

    /** @var string $meta_var */
    protected $meta_var = 'VALUE';

    /**
     * @param int $flags
     * @param \Closure|null $callable
     */
    public function __construct($flags = 0, $callable = null)
    {
    }

    /**
     * set description
     * @param string $str
     * @return static return itself for chaining
     */
    public function description($str)
    {
        $this->description = $str;
        return $this;
    }

    /**
     * set default value
     * @alias default
     * @param mixed $value
     * @return static return itself for chaining
     */
    public function defaultValue($value)
    {
        $this->default = $value;
        return $this;
    }

    protected $alias_of_default = 'defaultValue';

    /**
     * set the argument optional by setting default value to ''
     * @return static return itself for chaining
     */
    public function optional()
    {
        return $this->defaultValue('');
    }

    // ============================================

    public function metaVar()
    {
        return $this->meta_var;
    }
}