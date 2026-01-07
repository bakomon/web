<?php

// Source: https://github.com/FakerPHP/Faker/blob/c5c3935a6d764e8b94dafb8001ba9f814b159411/src/Provider/UserAgent.php

namespace Faker;

require_once __DIR__ . '/base.php';
require_once __DIR__ . '/miscellaneous.php';

class UserAgentGenerator extends Base
{
    protected static $userAgents = ['firefox', 'chrome', 'opera', 'safari', 'msedge'];

    protected static $windowsPlatformTokens = [
        'Windows NT 6.2', 'Windows NT 6.1', 'Windows NT 6.0', 'Windows NT 5.2', 'Windows NT 5.1',
        'Windows NT 5.01', 'Windows NT 5.0', 'Windows NT 4.0', 'Windows 98; Win 9x 4.90', 'Windows 98',
        'Windows 95', 'Windows CE',
    ];

    /**
     * Possible processors on Linux
     */
    protected static $linuxProcessor = ['i686', 'x86_64'];

    /**
     * Mac processors (it also added U;)
     */
    protected static $macProcessor = ['Intel', 'PPC', 'U; Intel', 'U; PPC'];

    /**
     * Add as many languages as you like.
     */
    protected static $lang = ['en-US', 'sl-SI', 'nl-NL'];

    /**
     * Generate mac processor
     *
     * @return string
     */
    public static function macProcessor()
    {
        return static::randomElement(static::$macProcessor);
    }

    /**
     * Generate linux processor
     *
     * @return string
     */
    public static function linuxProcessor()
    {
        return static::randomElement(static::$linuxProcessor);
    }

    /**
     * Generate a random user agent
     *
     * @example 'Mozilla/5.0 (Windows CE) AppleWebKit/5350 (KHTML, like Gecko) Chrome/13.0.888.0 Safari/5350'
     *
     * @return string
     */
    public static function userAgent()
    {
        $userAgentName = static::randomElement(static::$userAgents);

        return static::$userAgentName();
    }

    /**
     * Generate Chrome user agent
     *
     * @example 'Mozilla/5.0 (Macintosh; PPC Mac OS X 10_6_5) AppleWebKit/5312 (KHTML, like Gecko) Chrome/14.0.894.0 Safari/5312'
     *
     * @return string
     */
    public static function chrome()
    {
        $saf = self::numberBetween(531, 536) . self::numberBetween(0, 2);

        $platforms = [
            '(' . static::linuxPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . self::numberBetween(36, 40) . '.0.' . self::numberBetween(800, 899) . ".0 Mobile Safari/$saf",
            '(' . static::windowsPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . self::numberBetween(36, 40) . '.0.' . self::numberBetween(800, 899) . ".0 Mobile Safari/$saf",
            '(' . static::macPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . self::numberBetween(36, 40) . '.0.' . self::numberBetween(800, 899) . ".0 Mobile Safari/$saf",
        ];

        return 'Mozilla/5.0 ' . static::randomElement($platforms);
    }

    /**
     * Generate Edge user agent
     *
     * @example 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.82 Safari/537.36 Edg/99.0.1150.36'
     *
     * @return string
     */
    public static function msedge()
    {
        $saf = self::numberBetween(531, 537) . '.' . self::numberBetween(0, 2);
        $chrv = self::numberBetween(79, 99) . '.0';

        $platforms = [
            '(' . static::windowsPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/$chrv" . '.' . self::numberBetween(4000, 4844) . '.' . self::numberBetween(10, 99) . " Safari/$saf Edg/$chrv" . self::numberBetween(1000, 1146) . '.' . self::numberBetween(0, 99),
            '(' . static::macPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/$chrv" . '.' . self::numberBetween(4000, 4844) . '.' . self::numberBetween(10, 99) . " Safari/$saf Edg/$chrv" . self::numberBetween(1000, 1146) . '.' . self::numberBetween(0, 99),
            '(' . static::linuxPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/$chrv" . '.' . self::numberBetween(4000, 4844) . '.' . self::numberBetween(10, 99) . " Safari/$saf EdgA/$chrv" . self::numberBetween(1000, 1146) . '.' . self::numberBetween(0, 99),
            '(' . static::iosMobileToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Version/15.0 EdgiOS/$chrv" . self::numberBetween(1000, 1146) . '.' . self::numberBetween(0, 99) . " Mobile/15E148 Safari/$saf",
        ];

        return 'Mozilla/5.0 ' . static::randomElement($platforms);
    }

    /**
     * Generate Firefox user agent
     *
     * @example 'Mozilla/5.0 (X11; Linuxi686; rv:7.0) Gecko/20101231 Firefox/3.6'
     *
     * @return string
     */
    public static function firefox()
    {
        $ver = 'Gecko/' . date('Ymd', self::numberBetween(strtotime('2010-1-1'), time())) . ' Firefox/' . self::numberBetween(35, 37) . '.0';

        $platforms = [
            '(' . static::windowsPlatformToken() . '; ' . static::randomElement(static::$lang) . '; rv:1.9.' . self::numberBetween(0, 2) . '.20) ' . $ver,
            '(' . static::linuxPlatformToken() . '; rv:' . self::numberBetween(5, 7) . '.0) ' . $ver,
            '(' . static::macPlatformToken() . ' rv:' . self::numberBetween(2, 6) . '.0) ' . $ver,
        ];

        return 'Mozilla/5.0 ' . static::randomElement($platforms);
    }

    /**
     * Generate Safari user agent
     *
     * @example 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_7_1 rv:3.0; en-US) AppleWebKit/534.11.3 (KHTML, like Gecko) Version/4.0 Safari/534.11.3'
     *
     * @return string
     */
    public static function safari()
    {
        $saf = self::numberBetween(531, 535) . '.' . self::numberBetween(1, 50) . '.' . self::numberBetween(1, 7);

        if (Miscellaneous::boolean()) {
            $ver = self::numberBetween(4, 5) . '.' . self::numberBetween(0, 1);
        } else {
            $ver = self::numberBetween(4, 5) . '.0.' . self::numberBetween(1, 5);
        }

        $platforms = [
            '(Windows; U; ' . static::windowsPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf",
            '(' . static::macPlatformToken() . ' rv:' . self::numberBetween(2, 6) . '.0; ' . static::randomElement(static::$lang) . ") AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf",
        ];

        return 'Mozilla/5.0 ' . static::randomElement($platforms);
    }

    /**
     * Generate Safari Mobile user agent
     *
     * @example 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_3 like Mac OS X) AppleWebKit/540.8.15 (KHTML, like Gecko) Version/16.5.2 Mobile/9A115 Safari/604.26'
     *
     * @return string
     */
    public static function safariMobile()
    {
        $iosVer = self::numberBetween(9, 17) . '_' . self::numberBetween(0, 7) . '_' . self::numberBetween(1, 11); // https://en.wikipedia.org/wiki/IOS_version_history
        $safariVer = self::numberBetween(9, 17) . '.' . self::numberBetween(0, 6) . '.' . self::numberBetween(1, 3); // https://betawiki.net/wiki/Safari
        $appleWebKit = self::numberBetween(536, 621) . '.' . self::numberBetween(1, 8) . '.' . self::numberBetween(2, 50);
        $saf = self::numberBetween(601, 620) . '.' . self::numberBetween(1, 30);
        $build = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        $mobileDevices = [
            'iPhone; CPU iPhone OS',
            'iPad; CPU OS',
        ];

        $platforms = [
            '(' . static::randomElement($mobileDevices) . " $iosVer like Mac OS X) AppleWebKit/$appleWebKit (KHTML, like Gecko) Version/$safariVer Mobile/" . self::numberBetween(8, 21) . static::randomElement($build) . self::numberBetween(2, 466) . " Safari/$saf",
        ];

        return 'Mozilla/5.0 ' . static::randomElement($platforms);
    }

    /**
     * Generate Opera user agent
     *
     * @example 'Opera/8.25 (Windows NT 5.1; en-US) Presto/2.9.188 Version/10.00'
     *
     * @return string
     */
    public static function opera()
    {
        $platforms = [
            '(' . static::linuxPlatformToken() . '; ' . static::randomElement(static::$lang) . ') Presto/2.' . self::numberBetween(8, 12) . '.' . self::numberBetween(160, 355) . ' Version/' . self::numberBetween(10, 12) . '.00',
            '(' . static::windowsPlatformToken() . '; ' . static::randomElement(static::$lang) . ') Presto/2.' . self::numberBetween(8, 12) . '.' . self::numberBetween(160, 355) . ' Version/' . self::numberBetween(10, 12) . '.00',
        ];

        return 'Opera/' . self::numberBetween(8, 9) . '.' . self::numberBetween(10, 99) . ' ' . static::randomElement($platforms);
    }

    /**
     * @return string
     */
    public static function windowsPlatformToken()
    {
        return static::randomElement(static::$windowsPlatformTokens);
    }

    /**
     * @return string
     */
    public static function macPlatformToken()
    {
        return 'Macintosh; ' . static::randomElement(static::$macProcessor) . ' Mac OS X 10_' . self::numberBetween(5, 8) . '_' . self::numberBetween(0, 9);
    }

    /**
     * @return string
     */
    public static function iosMobileToken()
    {
        $iosVer = self::numberBetween(13, 15) . '_' . self::numberBetween(0, 2);

        return 'iPhone; CPU iPhone OS ' . $iosVer . ' like Mac OS X';
    }

    /**
     * @return string
     */
    public static function linuxPlatformToken()
    {
        return 'X11; Linux ' . static::randomElement(static::$linuxProcessor);
    }
}
