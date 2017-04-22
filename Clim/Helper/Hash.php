<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/20/17
 * Time: 9:52 AM
 */

namespace Clim\Helper;


class Hash implements \ArrayAccess
{
    protected $data;

    public function __construct($data = [], array ...$more)
    {
        $this->data = $data;
        foreach ($more as $array) {
            $this->update($array);
        }
    }

    public function all()
    {
        return $this->data;
    }

    public function push($value)
    {
        $this->data[] = $value;
    }

    public function set($name, $value, $append = false)
    {
        if ($append) {
            $this->append($name, $value);
        } else {
            $this->data[$name] = $value;
        }
    }

    public function append($offset, $value)
    {
        $elm = $this[$offset];

        if (is_null($elm)) {
            $elm = [$value];
        } else {
            if (is_array($elm)) {
                $elm[] = $value;
            } else {
                $elm = [$elm, $value];
            }
        }

        $this->data[$offset] = $elm;
    }

    public function update(array $array)
    {
        $this->data = array_replace($this->data, $array);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}