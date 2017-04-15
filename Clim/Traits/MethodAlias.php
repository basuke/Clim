<?php

namespace Clim\Traits;

trait MethodAlias
{
    public function __call($name, $args)
    {
        $alias = 'alias__'. $name;

        if (is_callable([$this, $alias])) {
            return call_user_func_array([$this, $alias], $args);
        } else {
            throw new \BadMethodCallException("Not a valid method: {$name}");
        }
    }
}

