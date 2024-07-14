<?php

namespace Api\Services;

require_once __DIR__ . '/hQuery.php';

use \DOMDocument;
use Api\Services\hQuery;

class Http
{
    private static $instance;
    public static $status;
    public static $cache;
    public static $bypass;
    public static $bypass_url;
    public static $link;
    public static $headers;
    public static $source;
    public static $error;

    public static function load(String $url, $options = [])
    {
        $is_bypass = isset($options['bypass']) && $options['bypass'] == true;
        $headers = isset($options['headers']) ? $options['headers'] : null;
        $fields = isset($options['fields']) ? $options['fields'] : null;

        if (isset($options['method']) && $options['method'] == 'POST') {
            $ch = hQuery::fromUrl($url, $headers, $fields, ['method' => 'POST']);
        } else {
            $ch = hQuery::fromUrl($url, $headers);
        }

        self::$status = $ch->code;
        self::$bypass = $is_bypass;
        if ($is_bypass) self::$bypass_url = $url;
        self::$link = $is_bypass ? $options['source_url'] : $url;

        if (isset($ch->error)) {
            self::$error = [
                'message' => $ch->error->getMessage(),
                'line' => $ch->error->getLine(),
                'file' => $ch->error->getFile(),
            ];
        } else {
            if ($is_bypass && self::$status == 200 && strpos($url, 'scrapingant') !== FALSE) {
                $response = json_decode($ch->body);

                if (count($response->headers) > 0) {
                    foreach ($response->headers as $i => $header) $header_list[strtoupper($header->name)] = $header->value;
                }

                self::$status = $response->status_code;
                self::$headers = $response->headers;
                self::$source = $response->html;
            } else {
                self::$headers = $ch->headers;
                self::$source = preg_match("//u", $ch->body) ? $ch->body : mb_convert_encoding($ch->body, 'UTF-8', mb_list_encodings()); //https://php.watch/versions/8.2/utf8_encode-utf8_decode-deprecated#utf8_encode-any-mbstring
            }
            self::$cache = $ch->cache;
        }

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function response()
    {
        return self::$source;
    }

    public static function responseString()
    {
        return htmlspecialchars(self::$source, ENT_QUOTES);
    }

    public static function responseParse()
    {
        $dom = new DOMDocument();
        $response = self::$source;
        $utf8 = 'http-equiv="Content-Type" content="text/html; charset=utf-8"';
        if (strpos($response, $utf8) === false) $response = str_replace('<head>', "<head><meta {$utf8} />" . $utf8, $response);
        @$dom->loadHTML($response);
        return $dom;
    }

    public static function getStatus()
    {
        return self::$status;
    }

    public static function isSuccess()
    {
        return self::$status == 200;
    }

    public static function isBlocked()
    {
        return self::$status == 403 && preg_match('/cloudflare|uvicorn/i', self::$headers['SERVER']);
    }

    public static function isDomainChanged($dom)
    {
        $url = $dom->query("//link[@rel='canonical' or contains(@href, '/feed')]")[0]->getAttribute('href');
        return parse_url(self::$link, PHP_URL_HOST) != parse_url($url, PHP_URL_HOST);
    }

    public static function bypass(String $url, $post = null)
    {
        $source = 'scrapingant';
        $lists = [
            "scrapingant" => [
                'api' => 'YOUR_SCRAPINGANT_APIKEY',
                'url' => 'https://api.scrapingant.com/v2/extended?x-api-key={apikey}&url=',
                'params' => '&browser=false&proxy_country=ID',
            ],
            "webscraping" => [
               'api' => 'YOUR_WEBSCRAPINGAI_APIKEY',
                'url' => 'https://api.webscraping.ai/html?api_key={apikey}&url=',
                'params' => '&js=false',
            ],
            "zenscrape" => [
               'api' => 'YOUR_ZENSCRAPE_APIKEY',
                'url' => 'https://app.zenscrape.com/api/v1/get?apikey={apikey}&url=',
                'params' => '',
            ]
        ];

        if (empty($lists[$source]['api'])) {
            $options = [];
            if ($post) $options['method'] = 'POST';
            return self::load($url, $options);
        } else {
            $full_url = str_replace('{apikey}', $lists[$source]['api'], $lists[$source]['url']) . urlencode($url) . $lists[$source]['params'];
            $options = ['bypass' => true, 'source_url' => $url];
            if ($post) $options['method'] = 'POST';
            return self::load($full_url, $options);
        }
    }

    public static function showError($message = null)
    {
        $error_message = 'Terjadi kesalahan';

        if (self::$status == 522) {
            $error_message = 'Connection timed out';
        } else if (self::$status >= 500) {
            $error_message = 'Server Error';
        } else if (self::$status == 404) {
            $error_message = 'Page Not Found';
        } else if (self::$status >= 400) {
            $error_message = 'Client Error';
        } else if ($message) {
            $error_message = $message;
        }

        $error =  [
            'status' => strtoupper(str_replace(' ', '_', preg_replace('/[^\sA-Za-z0-9]/', '', $error_message))),
            'status_code' => self::$status,
            'message' => $error_message,
            'bypass' => self::$bypass,
            'source' => self::$link,
        ];

        if (self::$bypass) {
            if (strpos(self::$bypass_url, 'scrapingant') !== FALSE) $error['message'] = json_decode(self::$source)->detail;
            $error['bypass_url'] = self::$bypass_url;
        }

        if (self::$status != 404 && self::$error == NULL) {
            $error['headers'] = self::$headers;
            $error['response'] = self::$source;
        }

        if (self::$error) $error['response'] = self::$error['message'] . ' in ' . self::$error['file'] . ' on line ' . self::$error['line'];

        return $error;
    }
}
