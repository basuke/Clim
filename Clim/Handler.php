<?php

namespace Clim;

class Handler
{
    /** @const string */
    const DEFINITION_PARSER = '/ \\s* (.+) \\s* \\{ \\s* (.*) \\s* \\} \\s* (.*) \\s* /x';

    /** @var string $definition */
    protected $definition;

    /** @var null $flags */
    protected $flags;

    /** @var \Closure $callable */
    protected $callable;

    /** @var bool whether the definition is compiled or not */
    protected $defined = false;

    /** @var string */
    protected $description = '';

    /** @var mixed value */
    protected $default = null;

    /** @var string $meta_var */
    protected $meta_var = 'VALUE';

    /** @var string $pattern */
    protected $pattern;

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
        $this->description = $str;
        return $this;
    }

    /**
     * set default value
     * @alias default
     * @param mixed $value
     * @return Handler return itself for chaining
     */
    public function defaultValue($value)
    {
        $this->default = $value;
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
        return $this->meta_var;
    }

    public function needDefined()
    {
        if ($this->defined) return;
        $this->defined = true;


        if (preg_match(self::DEFINITION_PARSER, $this->definition, $matches)) {
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

            $this->meta_var = trim(substr($meta_var, 0, $pos));
        } else {
            $this->meta_var = trim($meta_var);
        }
    }

    protected function evaluatePattern($pattern)
    {
        $this->pattern = '/^'. str_replace('/', '\\/', $pattern). '$/';
        if (@preg_match($this->pattern, null) === false) {
            throw new Exception\DefinitionException("invalid regular expression pattern");
        }
    }

    protected function evaluateNote($str)
    {
    }

    use Helper\MethodAliasTrait;

    protected $alias_of_default = 'defaultValue';
}