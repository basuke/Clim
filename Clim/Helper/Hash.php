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

    public static function merge(array $a1, array ...$args)
    {
        foreach ($args as $arg) {
            foreach ($arg as $key => $value) {
                if (is_int($key)) {
                    $a1[] = $value;
                } else {
                    if (isset($a1[$key]) && is_array($a1[$key]) && is_array($value)) {
                        $value = static::merge($a1[$key], $value);
                    }

                    $a1[$key] = $value;
                }
            }
        }
        return $a1;
    }

    public static function subset(array $array, array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = isset($array[$key]) ? $array[$key] : null;
        }
        return $result;
    }

    public function __construct($array = [], array ...$args)
    {
        $this->data = static::merge($array, ...$args);
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
        $this->data = static::merge($this->data, $array);
        return $this;
    }

    protected function enhash($array)
    {
        if (!is_array($array)) return $array;

        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $this->enhash($value);
        }
        return new Hash($result);
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