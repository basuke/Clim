<?php

namespace Clim;

class OptionParser extends Handler
{
    /** @var array $options */
    protected $options = [];

    /** @var bool $_need_value */
    protected $_need_value = false;

    public function parse($option, Context $context)
    {
        $this->needDefined();

        if (!$this->match($option)) return false;

        if ($this->_need_value) {
            $value = $context->tentative();

            if (is_null($value)) {
                $value = $context->hasMore() ? $context->next() : '';
            }

            if ($this->_pattern) {
                if (!preg_match($this->_pattern, $value)) {
                    throw new Exception\OptionException("pattern does't match");
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
        if (!is_null($this->_default)) {
            $this->needDefined();

            foreach ($this->options as $key) {
                if (!$context->has($key)) {
                    $context->set($key, $this->_default);
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
        return $this->_need_value;
    }

    protected function evaluateBody($str)
    {
        foreach (explode('|', $str) as $def) {
            $def = trim($def);
            if (strlen($def) == 0) continue;

            if (substr($def, 0, 2) == '--') {
                $this->options[] = substr($def, 2);
            } elseif (substr($def, 0, 1) == '-') {
                $this->options[] = substr($def, 1);
            }
        }
    }

    protected function evaluateMeta($meta_var)
    {
        $this->_need_value = true;
        parent::evaluateMeta($meta_var);
    }
}