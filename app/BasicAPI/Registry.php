<?php

namespace BasicAPI;

class Registry
{
    private static array $settings;

    private function __construct()
    {
    }

    /**
     * You can set a parameter only once
     * @param $key
     * @param $value
     * @return bool
     */
    public static function set($key, $value)
    {
        if (!isset(self::$settings[$key])) {
            self::$settings[$key] = $value;
            return true;
        } else {
            return false;
        }
    }

    public static function get($key)
    {
        if (isset(self::$settings[$key])) {
            return self::$settings[$key];
        } else {
            return null;
        }
    }
}