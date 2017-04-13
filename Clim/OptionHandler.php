<?php

namespace Clim;

class OptionHandler
{
    /** @var string $definition */
    protected $definition;

    /** @var null $flags */
    protected $flags;

    /** @var \Closure $callable */
    protected $callable;

    /** @var array $options */
    protected $options = [];

    /** @var bool $_need_value */
    protected $_need_value = false;

    /** @var string $_description */
    protected $_description = '';

    /** @var string $_meta_var */
    protected $_meta_var = 'VALUE';

    /** @var string $_pattern */
    protected $_pattern;

    /**
     * @param string $definition
     * @param int $flags
     * @param \Closure|null $callable
     */
    public function __construct($definition, $flags = 0, $callable = null)
    {
        $this->definition = $definition;
        $this->flags = $flags;
        $this->callable = $callable;
    }

    public function description($str = null)
    {
        if (is_null($str)) {
            return $this->_description;
        } else {
            $this->_description = $str;
            return $this;
        }
    }

    public function handle($option, Context $context)
    {
        $this->parse();

        if (!$this->match($option)) return false;

        if ($this->_need_value) {
            $value = $context->tentative();

            if (is_null($value)) {
                $value = $context->hasNext() ? $context->next() : '';
            }

            if ($this->_pattern) {
                if (!preg_match($this->_pattern, $value)) {
                    throw new Exception\OptionException();
                }
            }

            $context[$option] = $value;
        } else {
            $context[$option] = true;
        }

        return true;
    }

    public function match($option)
    {
        $this->parse();
        return in_array($option, $this->options);
    }

    public function needValue()
    {
        $this->parse();
        return $this->_need_value;
    }

    public function metaVar()
    {
        $this->parse();
        return $this->_meta_var;
    }

    public function parse()
    {
        if (!empty($this->options)) return;

        $str = $this->definition;
        if (preg_match('/ (.+) \\{ (.*) \\} (.*) /x', $str, $matches)) {
            $this->_need_value = true;

            $meta_var = $matches[2];
            $pos = strpos($meta_var, '|');
            if ($pos !== false) {
                $pattern = trim(substr($meta_var, $pos + 1));
                $this->_pattern = '/^'. str_replace('/', '\\/', $pattern). '$/';
                if (@preg_match($this->_pattern, null) === false) {
                    throw new Exception\OptionException();
                }

                $this->_meta_var = trim(substr($meta_var, 0, $pos));
            } else {
                $this->_meta_var = trim($meta_var);
            }

            $str = $matches[1];
        }

        foreach (explode('|', $str) as $def) {
            $def = trim($def);
            if (substr($def, 0, 2) == '--') {
                $this->options[] = substr($def, 2);
            } elseif (substr($def, 0, 1) == '-') {
                $this->options[] = substr($def, 1);
            }
        }
    }
}