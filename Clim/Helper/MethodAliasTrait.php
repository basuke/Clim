<?php

namespace Clim\Helper;

trait MethodAliasTrait
{
    public function __call($name, $args)
    {
        $alias = 'alias_of_'. $name;

        if (isset($this->{$alias}) && $this->{$alias}) {
            return call_user_func_array([$this, $this->{$alias}], $args);
        } else {
            throw new \BadMethodCallException("Not a valid method: {$name}");
        }
    }
}

