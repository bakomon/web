<?php

// Source: https://github.com/FakerPHP/Faker/blob/c5c3935a6d764e8b94dafb8001ba9f814b159411/src/Provider/Miscellaneous.php

namespace Faker;

require_once __DIR__ . '/base.php';

class Miscellaneous extends Base
{
    /**
     * Return a boolean, true or false.
     *
     * @param int $chanceOfGettingTrue Between 0 (always get false) and 100 (always get true)
     *
     * @return bool
     *
     * @example true
     */
    public static function boolean($chanceOfGettingTrue = 50)
    {
        return self::numberBetween(1, 100) <= $chanceOfGettingTrue;
    }
}
