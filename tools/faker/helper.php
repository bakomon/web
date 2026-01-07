<?php

// Source: https://github.com/FakerPHP/Faker/blob/c5c3935a6d764e8b94dafb8001ba9f814b159411/src/Extension/Helper.php

namespace Faker;

/**
 * A class with some methods that may make building extensions easier.
 *
 * @experimental This class is experimental and does not fall under our BC promise
 */
final class Helper
{
    /**
     * Returns a random element from a passed array.
     */
    public static function randomElement(array $array)
    {
        if ($array === []) {
            return null;
        }

        return $array[array_rand($array, 1)];
    }

    public static function randomNumberBetween(int $min, int $max): int
    {
        return mt_rand($min, $max);
    }
}
