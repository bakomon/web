<?php

namespace Api;

class Allowed
{
    public static $site_lists = ['localhost'];

    public function check()
    {
        array_push(self::$site_lists, $_SERVER['SERVER_NAME']);
        $rgx_escape = implode('|', array_map(function($value) { return preg_quote($value, '/'); }, self::$site_lists));

        if (isset($_SERVER['HTTP_REFERER']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'xmlhttprequest') :
            // if (in_array($_SERVER['HTTP_REFERER'], $allowedReferers)) : //https://stackoverflow.com/a/50684639
            if (preg_match("/$rgx_escape/", $_SERVER['HTTP_REFERER'])) :
                return true;
            else :
                return false;
            endif;
        else :
            return false;
        endif;
    }
}
