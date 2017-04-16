<?php

namespace Clim;

class Handler
{
    /** @const string */
    const definition_parser = '/ \\s* (.+) \\s* \\{ \\s* (.*) \\s* \\} \\s* (.*) \\s* /x';

    /** @var string $definition */
    protected $definition;

    /** @var null $flags */
    protected $flags;

    /** @var \Closure $callable */
    protected $callable;

    /** @var bool whether the definition is compiled or not */
    protected $_defined = false;

    /** @var string */
    protected $_description = '';

    /** @var mixed value */
    protected $_default = null;

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

    /**
     * set description
     * @param string $str
     * @return Handler return itself for chaining
     */
    public function description($str)
    {
        $this->_description = $str;
        return $this;
    }

    /**
     * set default value
     * @alias default
     * @param mixed value $value
     * @return Handler return itself for chaining
     */
    public function defaultValue($value)
    {
        $this->_default = $value;
        return $this;
    }

    /**
     * set the argument optional by setting default value to ''
     * @return Handler return itself for chaining
     */
    public function optional()
    {
        return $this->defaultValue('');
    }

    // -------------------------------------------------------------------------

    public function metaVar()
    {
        $this->needDefined();
        return $this->_meta_var;
    }

    public function needDefined()
    {
        if ($this->_defined) return;
        $this->_defined = true;


        if (preg_match(self::definition_parser, $this->definition, $matches)) {
            $this->evaluateBody($matches[1]);
            $this->evaluateMeta($matches[2]);
            $this->evaluateNote($matches[3]);
        } else {
            $this->evaluateBody($this->definition);
        }
    }

    protected function evaluateBody($str)
    {
    }

    protected function evaluateMeta($meta_var)
    {
        $pos = strpos($meta_var, '|');
        if ($pos !== false) {
            $this->evaluatePattern(trim(substr($meta_var, $pos + 1)));

            $this->_meta_var = trim(substr($meta_var, 0, $pos));
        } else {
            $this->_meta_var = trim($meta_var);
        }
    }

    protected function evaluatePattern($pattern)
    {
        $this->_pattern = '/^'. str_replace('/', '\\/', $pattern). '$/';
        if (@preg_match($this->_pattern, null) === false) {
            throw new Exception\DefinitionException("invalid regular expression pattern");
        }
    }

    protected function evaluateNote($str)
    {
    }

    use Traits\MethodAlias;

    protected $alias__default = 'defaultValue';
}