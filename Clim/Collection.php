<?php

namespace Clim;

class Collection extends \Slim\Collection
{
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    public function unshift($value)
    {
        array_unshift($this->data, $value);
        return $this;
    }

    public function shift()
    {
        return array_shift($this->data);
    }

    public function push($value)
    {
        array_push($this->data, $value);
        return $this;
    }

    public function pop()
    {
        return array_pop($this->data);
    }

    public function prepend($values)
    {
        $this->data = array_merge(self::toArray($values), $this->data);
        return $this;
    }

    public function append($values)
    {
        $this->data = array_merge($this->data, self::toArray($values));
        return $this;
    }

    protected static function toArray($values)
    {
        if ($values instanceof \Slim\Collection) {
            return $values->all();
        }

        return (array) $values;
    }
}