<?php

namespace Tools;

class cURL
{
    private static $instance;
    public static $source;
    public static $headers;
    public static $status;
    public static $link;

    public static function get(String $url, $options = [])
    {
        $header_list = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (isset($options['headers'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        if (isset($options['useragent'])) curl_setopt($ch, CURLOPT_USERAGENT, $options['useragent']);
        if (isset($options['referer'])) curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
        if (isset($options['ignore_ssl']) && $options['ignore_ssl'] === true) {
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        // this function is called for each header received https://stackoverflow.com/a/41135574
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
          function($curl, $header_line) use (&$header_list)
          {
            $len = strlen($header_line);
            $header = explode(':', $header_line, 2);
            if (count($header) < 2) { //ignore invalid headers
              return $len;
            }

            $header_list[strtolower(trim($header[0]))] = trim($header[1]);

            return $len;
          }
        );

        self::$source = curl_exec($ch);
        self::$headers = $header_list;
        self::$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        if (isset($options['fields'])) curl_setopt($ch, CURLOPT_POSTFIELDS, $options['fields']);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3); // or use CURL_REDIR_POST_ALL
        if (isset($options['headers'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        if (isset($options['useragent'])) curl_setopt($ch, CURLOPT_USERAGENT, $options['useragent']);
        if (isset($options['referer'])) curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
        if (isset($options['ignore_ssl']) && $options['ignore_ssl'] === true) {
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        // this function is called for each header received https://stackoverflow.com/a/41135574
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
          function($curl, $header_line) use (&$header_list)
          {
            $len = strlen($header_line);
            $header = explode(':', $header_line, 2);
            if (count($header) < 2) { //ignore invalid headers
              return $len;
            }

            $header_list[strtolower(trim($header[0]))] = trim($header[1]);

            return $len;
          }
        );

        self::$source = curl_exec($ch);
        self::$headers = $header_list;
        self::$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        self::$link = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
