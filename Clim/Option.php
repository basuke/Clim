<?php

namespace Clim;

use Clim\Cli\Component;
use Clim\Exception\OptionException;
use Clim\Helper\DeferredDefinitionTrait;

class Option extends Component
{
    use DeferredDefinitionTrait;

    /** @var array $options */
    protected $options = [];

    /** @var bool $need_value */
    protected $need_value = false;

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

        parent::__construct($flags, $callable);
    }

    public function parse($option, Context $context)
    {
        $this->needDefined();

        if (!$this->match($option)) return false;

        if ($this->need_value) {
            $value = $context->tentative();

            if (is_null($value)) {
                $value = $context->hasMore() ? $context->next() : '';
            }

            if ($this->pattern) {
                if (!preg_match($this->pattern, $value)) {
                    throw new OptionException("pattern does't match");
                }
            }
        } else {
            $value = true;
        }

        foreach ($this->options as $key) {
            $context->set($key, $value);
        }
        return true;
    }

    public function collectDefaultValue(Context $context)
    {
        if (!is_null($this->default)) {
            $this->needDefined();

            foreach ($this->options as $key) {
                if (!$context->has($key)) {
                    $context->set($key, $this->default);
                }
            }
        }
    }

    public function match($option)
    {
        $this->needDefined();
        return in_array($option, $this->options);
    }

    public function needValue()
    {
        $this->needDefined();
        return $this->need_value;
    }

    protected function define($body, $name, $pattern, $note)
    {
        if ($body) {
            foreach (explode('|', $body) as $def) {
                $def = trim($def);
                if (strlen($def) == 0) continue;

                if (substr($def, 0, 2) == '--') {
                    $this->options[] = substr($def, 2);
                } elseif (substr($def, 0, 1) == '-') {
                    $this->options[] = substr($def, 1);
                }
            }
        }

        if ($name) {
            $this->need_value = true;
            $this->meta_var = $name;
        }

        $this->pattern = $pattern;
    }
}