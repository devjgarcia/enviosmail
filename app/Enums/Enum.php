<?php

namespace App\Enums;

abstract class Enum
{
    protected static $definitions = [];

    public static function all(): array
    {
        return static::$definitions;
    }

    public static function keys(): array
    {
        return array_keys(static::$definitions);
    }

    public static function name($key): string
    {
        return static::$definitions[$key] ?? '';
    }
}