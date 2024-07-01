<?php

namespace Api\Services;

use \DOMDocument;

class Http
{
    private static $instance;
    public static $source;
    public static $headers;
    public static $status;
    public static $link;
    public static $bypass;

    public static function get(String $url, $options = [])
    {
        $header_list = [];
        $is_bypass = isset($options['bypass']) && $options['bypass'] == true;

        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome Privacy Preserving Prefetch Proxy');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (isset($options['headers'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);

        if (!$is_bypass) {
            // this function is called for each header received https://stackoverflow.com/a/41135574/7598333
            curl_setopt($ch, CURLOPT_HEADERFUNCTION,
              function($curl, $header_line) use (&$header_list)
              {
                $len = strlen($header_line);
                $header = explode(':', $header_line, 2);
                if (count($header) < 2) // ignore invalid headers
                  return $len;
            
                $header_list[strtolower(trim($header[0]))] = trim($header[1]);
                
                return $len;
              }
            );
        }
        
        self::$source = curl_exec($ch);
        self::$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($is_bypass && self::$status == 200 && strpos($url, 'scrapingant') !== FALSE) {
            $response = json_decode(self::$source);

            if (count($response->headers) > 0) {
                foreach ($response->headers as $i => $header) $header_list[$header->name] = $header->value;
            }

            self::$source = $response->html;
            self::$headers = $response->headers;
            self::$status = $response->status_code;
        } else {
            self::$headers = $header_list;
        }
        self::$bypass = $is_bypass;
        self::$link = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function post(String $url, $options = [])
    {
        $header_list = [];
        $is_bypass = isset($options['bypass']) && $options['bypass'] == true;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        if (isset($options['fields'])) curl_setopt($ch, CURLOPT_POSTFIELDS, $options['fields']);
        if (isset($options['headers'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        
        if (!$is_bypass) {
            // this function is called for each header received https://stackoverflow.com/a/41135574/7598333
            curl_setopt($ch, CURLOPT_HEADERFUNCTION,
              function($curl, $header_line) use (&$header_list)
              {
                $len = strlen($header_line);
                $header = explode(':', $header_line, 2);
                if (count($header) < 2) // ignore invalid headers
                  return $len;
            
                $header_list[strtolower(trim($header[0]))] = trim($header[1]);
                
                return $len;
              }
            );
        }
        
        self::$source = curl_exec($ch);
        self::$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($is_bypass && self::$status == 200 && strpos($url, 'scrapingant') !== FALSE) {
            $response = json_decode(self::$source);

            if (count($response->headers) > 0) {
                foreach ($response->headers as $i => $header) $header_list[$header->name] = $header->value;
            }

            self::$source = $response->html;
            self::$headers = $response->headers;
            self::$status = $response->status_code;
        } else {
            self::$headers = $header_list;
        }
        self::$bypass = $is_bypass;
        self::$link = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

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
        return htmlspecialchars(self::$source);
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
        return self::$status == 403 && preg_match('/cloudflare|uvicorn/i', self::$headers['server']);
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

        $full_url = str_replace('{apikey}', $lists[$source]['api'], $lists[$source]['url']) . urlencode($url) . $lists[$source]['params'];
        return $post ? self::post($full_url, ['bypass' => true]) : self::get($full_url, ['bypass' => true]);
    }

    public static function showError()
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
        }

        $error =  [
            'status' => strtoupper(str_replace(' ', '_', $error_message)),
            'status_code' => self::$status,
            'message' => self::$bypass && strpos(self::$link, 'scrapingant') !== FALSE ? json_decode(self::$source)->detail : $error_message,
            'bypass' => self::$bypass,
            'source' => self::$link,
        ];

        if (self::$bypass) {
            $query_str = parse_url($error['source'], PHP_URL_QUERY);
            parse_str($query_str, $query);
            $error['source'] = urldecode($query['url']);
            $error['bypass_url'] = self::$link;
        }

        if (self::$status != 404) {
            $error['headers'] = self::$headers;
            $error['response'] = self::$source;
        }

        return $error;
    }
}
