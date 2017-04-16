<?php

namespace Clim\Traits;

trait MethodAlias
{
    public function __call($name, $args)
    {
        $alias = 'alias__'. $name;

        if (isset($this->{$alias}) && $this->{$alias}) {
            return call_user_func_array([$this, $this->{$alias}], $args);
        } else {
            throw new \BadMethodCallException("Not a valid method: {$name}");
        }
    }
}

