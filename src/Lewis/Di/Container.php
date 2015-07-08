<?php

namespace Lewis\Di;

class Container
{
    public static $data;

    public function get($name)
    {
        return static::$data[$name];
    }

    public function set($name, $value)
    {
        static::$data[$name] = $value;
    }
}