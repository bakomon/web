<?php

// Source: https://github.com/FakerPHP/Faker/blob/c5c3935a6d764e8b94dafb8001ba9f814b159411/src/Provider/Base.php

namespace Faker;

require_once __DIR__ . '/helper.php';

class Base
{

    /**
     * Returns a random number between $int1 and $int2 (any order)
     *
     * @param int $int1 default to 0
     * @param int $int2 defaults to 32 bit max integer, ie 2147483647
     *
     * @example 79907610
     *
     * @return int
     */
    public static function numberBetween($int1 = 0, $int2 = 2147483647)
    {
        $min = $int1 < $int2 ? $int1 : $int2;
        $max = $int1 < $int2 ? $int2 : $int1;

        return Helper::randomNumberBetween($min, $max);
    }

    /**
     * Returns randomly ordered subsequence of $count elements from a provided array
     *
     * @todo update default $count to `null` (BC) for next major version
     *
     * @param array|class-string|\Traversable $array           Array to take elements from. Defaults to a-c
     * @param int|null                        $count           Number of elements to take. If `null` then returns random number of elements
     * @param bool                            $allowDuplicates Allow elements to be picked several times. Defaults to false
     *
     * @throws \InvalidArgumentException
     * @throws \LengthException          When requesting more elements than provided
     *
     * @return array New array with $count elements from $array
     */
    public static function randomElements($array = ['a', 'b', 'c'], $count = 1, $allowDuplicates = false)
    {
        $elements = $array;

        if (is_string($array) && function_exists('enum_exists') && enum_exists($array)) {
            $elements = $array::cases();
        }

        if ($array instanceof \Traversable) {
            $elements = iterator_to_array($array, false);
        }

        if (!is_array($elements)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument for parameter $array needs to be array, an instance of %s, or an instance of %s, got %s instead.',
                \UnitEnum::class,
                \Traversable::class,
                is_object($array) ? get_class($array) : gettype($array),
            ));
        }

        $numberOfElements = count($elements);

        if (!$allowDuplicates && null !== $count && $numberOfElements < $count) {
            throw new \LengthException(sprintf(
                'Cannot get %d elements, only %d in array',
                $count,
                $numberOfElements,
            ));
        }

        if (null === $count) {
            $count = Helper::randomNumberBetween(1, $numberOfElements);
        }

        $randomElements = [];

        $keys = array_keys($elements);
        $maxIndex = $numberOfElements - 1;
        $elementHasBeenSelectedAlready = [];
        $numberOfRandomElements = 0;

        while ($numberOfRandomElements < $count) {
            $index = Helper::randomNumberBetween(0, $maxIndex);

            if (!$allowDuplicates) {
                if (isset($elementHasBeenSelectedAlready[$index])) {
                    continue;
                }

                $elementHasBeenSelectedAlready[$index] = true;
            }

            $key = $keys[$index];

            $randomElements[] = $elements[$key];

            ++$numberOfRandomElements;
        }

        return $randomElements;
    }

    /**
     * Returns a random element from a passed array
     *
     * @param array|class-string|\Traversable $array
     *
     * @throws \InvalidArgumentException
     */
    public static function randomElement($array = ['a', 'b', 'c'])
    {
        $elements = $array;

        if (is_string($array) && function_exists('enum_exists') && enum_exists($array)) {
            $elements = $array::cases();
        }

        if ($array instanceof \Traversable) {
            $elements = iterator_to_array($array, false);
        }

        if ($elements === []) {
            return null;
        }

        if (!is_array($elements)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument for parameter $array needs to be array, an instance of %s, or an instance of %s, got %s instead.',
                \UnitEnum::class,
                \Traversable::class,
                is_object($array) ? get_class($array) : gettype($array),
            ));
        }

        $randomElements = static::randomElements($elements, 1);

        return $randomElements[0];
    }
}
