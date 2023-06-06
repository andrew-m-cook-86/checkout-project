<?php
declare(strict_types=1);

namespace App\Traits;

Trait EnumHelpers
{
    public static function names(): array
    {
        return array_map('strtolower', array_column(self::cases(), 'name'));
    }

    public static function fromName(string $name){

        return constant("self::$name");
    }

    public static function namesToString(): string
    {
        return implode(",", array_column(self::cases(), 'name'));
    }

    public static function exists($name): bool
    {
        return in_array(strtolower($name), self::names());
    }
}
