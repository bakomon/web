<?php

namespace Api\Services;

require_once __DIR__ . '/hQuery.php';
require_once __DIR__ . '/Escape.php';
require_once dirname(__DIR__, 2) . '/tools/dot-env.php';

use \DOMDocument;
use Tools\DotEnv;
use Api\Services\hQuery;
use Api\Services\Escape;

(new DotEnv(dirname(__DIR__, 2) . '/.env'))->load();

class Http
{
    public $status;
    public $cache;
    public $bypass;
    public $bypass_url;
    public $link;
    public $headers;
    public $source;
    public $error;
    public static $proxy;
    public static $proxy_host;

    public function __construct()
    {
        // Initialize properties
        $this->status = null;
        $this->cache = null;
        $this->bypass = false;
        $this->bypass_url = null;
        $this->link = null;
        $this->headers = null;
        $this->source = null;
        $this->error = null;
    }

    public static function load(String $url, $options = [])
    {
        $instance = new self();
        $is_bypass = isset($options['bypass']) && $options['bypass'] == true;
        $headers = isset($options['headers']) ? $options['headers'] : null;
        $fields = isset($options['fields']) ? $options['fields'] : null;

        if (isset($options['method']) && $options['method'] == 'POST') {
            $ch = hQuery::fromUrl($url, $headers, $fields, ['method' => 'POST', 'ignore_ssl' => true]);
        } else {
            $ch = hQuery::fromUrl($url, $headers, null, ['ignore_ssl' => true]);
        }

        $instance->status = $ch->code;
        $instance->bypass = $is_bypass;
        if ($is_bypass) $instance->bypass_url = $url;
        $instance->link = $is_bypass ? $options['source_url'] : $url;

        if (isset($ch->error)) {
            $instance->error = [
                'message' => $ch->error->getMessage(),
                'line' => $ch->error->getLine(),
                'file' => $ch->error->getFile(),
            ];
        } else {
            if ($is_bypass && $instance->status == 200 && strpos($url, 'scrapingant') !== FALSE) {
                $response = json_decode($ch->body);

                if (count($response->headers) > 0) {
                    foreach ($response->headers as $i => $header) $header_list[strtoupper($header->name)] = $header->value;
                }

                $instance->status = $response->status_code;
                $instance->headers = $response->headers;
                $instance->source = $response->html;
            } else {
                $body = $ch->body ?? '';
                $instance->headers = $ch->headers;
                $instance->source = preg_match("//u", $body) ? $ch->body : mb_convert_encoding($body, 'UTF-8', mb_list_encodings()); //https://php.watch/versions/8.2/utf8_encode-utf8_decode-deprecated#utf8_encode-any-mbstring
            }
            $instance->cache = $ch->cache;
        }

        return $instance;
    }

    public function response()
    {
        return $this->source;
    }

    public function responseEntity()
    {
        return htmlspecialchars($this->source, ENT_QUOTES);
    }

    public function responseParse($options = 0)
    {
        $dom = new DOMDocument();
        $response = $this->escape();
        @$dom->loadHTML(mb_encode_numericentity($response, [0x80, 0x10FFFF, 0, ~0], 'UTF-8'), $options); //https://stackoverflow.com/a/8218649
        return $dom;
    }

    public function escape($response = null)
    {
        $escape = new Escape();
        $invalid = [
            '/\n?\s+[:@][\w\-\.]+="[^"]+"/', //remove invalid attributes. e.g. ":class" or "@click"
            '/\s(?!([\w:-]+)?(href|src|get|post|patch|put|url))[\w:-]+=([\'"])(?:(?!\3)[\s\S])*?[<>&](?:(?!\3)[\s\S])*?\3/', //remove attributes whose values contain "<, >, or &". skip attributes containing "href" or "src"
        ];
        $res = preg_replace($invalid, '', ($response ?? $this->source));
        $res = $escape->fix_html_comments($res);
        $res = $escape->fix_void_elements($res);
        $res = $escape->fix_boolean_attributes($res);
        return $res;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isSuccess()
    {
        return $this->status == 200;
    }

    public function isEmpty()
    {
        return $this->source == '' || $this->source == null ? true : false;
    }

    public function isBlocked($xpath = null)
    {
        $blocked = $xpath ? $xpath->query("//input[@id='wsidchk']") : null;
        return $this->status == 403 && preg_match('/cloudflare|uvicorn/i', $this->headers['SERVER']) || ($blocked && $blocked->length > 0);
    }

    public function isDomainChanged($xpath)
    {
        $lists = $xpath->query("//link[@rel='canonical' or contains(@href, '/feed')]");
        if ($lists->length > 0) {
            $changed = true;
            $url = '';
            foreach ($lists as $link) {
                $url = $link->getAttribute('href');
                if (empty($url)) continue;
                if (strpos($url, '://') === false && substr($url, 0, 1) != '/') $url = 'http://' . $url;

                if (parse_url($this->link, PHP_URL_HOST) == parse_url($url, PHP_URL_HOST)) $changed = false;
            }
            return self::$proxy || empty($url) ? false : $changed;
        } else {
            return false;
        }
    }

    public static function bypass(String $url, $options = [])
    {
        $source = 'scrapingant';
        $lists = [
            'scrapingant' => [
                'env' => 'SCRAPINGANT_APIKEY',
                'url' => 'https://api.scrapingant.com/v2/extended?x-api-key={apikey}&url=',
                'params' => '&browser=false&proxy_country=ID',
            ],
            'webscraping' => [
                'env' => 'WEBSCRAPINGAI_APIKEY',
                'url' => 'https://api.webscraping.ai/html?api_key={apikey}&url=',
                'params' => '&js=false',
            ],
            'zenscrape' => [
                'env' => 'ZENSCRAPE_APIKEY',
                'url' => 'https://app.zenscrape.com/api/v1/get?apikey={apikey}&url=',
                'params' => '',
            ]
        ];

        $apikey = isset($_ENV[$lists[$source]['env']]) ? $_ENV[$lists[$source]['env']] : null;

        if (empty($apikey)) {
            return self::load($url, $options);
        } else {
            $full_url = str_replace('{apikey}', $apikey, $lists[$source]['url']) . urlencode($url) . $lists[$source]['params'];
            $options = array_merge($options, ['bypass' => true, 'source_url' => $url]);
            return self::load($full_url, $options);
        }
    }

    public static function proxy(String $url, $options = [])
    {
        $source = 'HuaBofeng';
        $lists = [
            '1234567Yang' => 'https://y.demo.lhyang.org/',
            '1234567Yang_2' => '__PROXY_PWD__=maga2028|https://shengtai.edu.pastapexamsdownload.space/',
            'HuaBofeng' => 'https://p.lanni.site/',
            // 'wangwenzhiwwz' => 'https://p.wwz.im/',
            // 'SokWithMe' => 'https://xyp.pages.dev/',
        ];

        self::$proxy = true;
        self::$proxy_host = $lists[$source];

        if (strpos(self::$proxy_host, '|') !== false) {
            list($cookiePart, $hostPart) = explode('|', self::$proxy_host, 2);
            self::$proxy_host = $hostPart;

            if (!isset($options['headers']) || !is_array($options['headers'])) {
                $options['headers'] = [];
            }
            if (!empty($options['headers']['Cookie'])) {
                $options['headers']['Cookie'] .= '; ' . $cookiePart;
            } else {
                $options['headers']['Cookie'] = $cookiePart;
            }
        }

        $full_url = self::$proxy_host . $url;
        $options = array_merge($options, ['bypass' => true, 'source_url' => $url]);
        return self::load($full_url, $options);
    }

    public function showError($message = null)
    {
        $error_message = 'Terjadi kesalahan';

        if ($this->status == 522) {
            $error_message = 'Connection timed out';
        } else if ($this->status >= 500) {
            $error_message = 'Server Error';
        } else if ($this->status == 404) {
            $error_message = 'Page Not Found';
        } else if ($this->status >= 400) {
            $error_message = 'Client Error';
        } else if ($message) {
            $error_message = $message;
        }

        $error =  [
            'status' => strtoupper(str_replace(' ', '_', preg_replace('/[^\sA-Za-z0-9]/', '', $error_message))),
            'status_code' => $this->status,
            'message' => $error_message,
            'bypass' => $this->bypass,
            'source' => $this->link,
        ];

        if ($this->bypass) {
            if (strpos($this->bypass_url, 'scrapingant') !== FALSE) $error['message'] = json_decode($this->source)->detail;
            $error['bypass_url'] = $this->bypass_url;
        }

        if ($this->status != 404 && $this->error == NULL) {
            $error['headers'] = $this->headers;
            $error['response'] = $this->source;
        }

        if ($this->error) $error['response'] = $this->error['message'] . ' in ' . $this->error['file'] . ' on line ' . $this->error['line'];

        return $error;
    }
}
