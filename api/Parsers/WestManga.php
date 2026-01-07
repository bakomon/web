<?php

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Services\Http;
use Faker\UserAgentGenerator;

class WestMangaParser
{
    public $response;
    private $userAgent;
    private $domain = 'data.westmanga.me';
    private $accessKey = 'WM_WEB_FRONT_END';
    private $signatureEnc = 'U2FsdGVkX1/d15RGXgbiDR9ygJcJxQG6sP14ws+Uzzw=';
    private $signaturePass = "LuVwFghhgMGEbqptN4uY0osS45rQWHWxIZ+0oTb5jy8LuVwFghhgMGEbqptN4uY0osS45rQWHWxIZ+0oTb5jy8";

    public function __construct()
    {
        $this->userAgent = (new UserAgentGenerator)->userAgent();
    }

    public function getChapter($params)
    {
        $chapterSlug = $params['url'];
        $url = "https://$this->domain/api/v/" . str_replace('/view/', '', $chapterSlug);
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);

        $source = $this->toAbsoluteUrl($chapterSlug, $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $chapter = $result['data'];

        $current = array_search($chapter['slug'], array_column($chapter['chapters'], 'slug'));

        if ($current > 0) {
            $next_data = $chapter['chapters'][$current - 1];
            $next = [
                'number' => $next_data['number'],
                'url' => '/view/' . $next_data['slug'],
            ];
        } else {
            $next = json_decode('{}');
        }

        if ($current < count($chapter['chapters']) - 1) {
            $prev_data = $chapter['chapters'][$current + 1];
            $prev = [
                'number' => $prev_data['number'],
                'url' => '/view/' . $prev_data['slug'],
            ];
        } else {
            $prev = json_decode('{}');
        }

        $img_lists = [];
        foreach ($chapter['images'] as $list) {
            array_push($img_lists, $list);
        }

        return [
            'title' => $chapter['content']['title'],
            'cover' => $chapter['content']['cover'],
            'current' => $chapter['number'],
            'next' => $next,
            'prev' => $prev,
            'source' => $source,
            'images' => $img_lists,
        ];
    }

    public function getSeries($slug)
    {
        $url = "https://$this->domain/api/comic/$slug";
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);

        $source = $this->toAbsoluteUrl("/comic/$slug", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua'];
        $series = $result['data'];

        $genres = [];
        foreach ($series['genres'] as $list) {
            array_push($genres, $list['name']);
        }

        $desc_pattern = ['/\n+/', '/<[^>]+>/'];
        $desc_replace = ["\n", ''];

        $chapters = [];
        foreach ($series['chapters'] as $list) {
            array_push($chapters, [
                'number' => (string)$list['number'],
                'url' => '/view/' . $list['slug'],
            ]);
        }

        return [
            'title' => $series['title'],
            'alternative' => isset($series['alternative_name']) && $series['alternative_name'] != '-' ? $series['alternative_name'] : '',
            'cover' => $series['cover'],
            'detail' => [
                'type' => $type[strtolower($series['country_id'])],
                'status' => $series['status'],
                'released' => $series['release'] ?? '',
                'author' => $series['author'] == '-' ? '' : $series['author'],
                'artist' => '',
                'genre' => implode(', ', $genres),
            ],
            'desc' => trim(preg_replace($desc_pattern, $desc_replace, $series['sinopsis'])),
            'source' => $source,
            'chapter' => $chapters,
        ];
    }

    public function getSearch($adv, $value, $page = 1, $display = 40)
    {
        $url = "https://$this->domain/api/contents?type=Comic&page=$page&per_page=$display";
        $url .= '&' . ($adv ? $value : "q=$value");

        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['paginator']['last_page'] ? $page + 1 : '',
            'lists' => [],
        ];

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua'];

        foreach ($result['data'] as $list) {
            array_push($data['lists'], [
                'title' => $list['title'],
                'cover' => $list['cover'],
                'type' => $type[strtolower($list['country_id'])],
                'color' => $list['color'],
                'completed' => $list['status'] == 'completed' ? true : false,
                'url' => $this->toAbsoluteUrl('/comic/' . $list['slug'], $this->domain),
                'slug' => $list['slug'],
            ]);
        }
        return $data;
    }

    public function getLatest($sortBy, $page = 1, $display = 40)
    {
        $sortOrder = [
            'library' => 'Added',
            'update' => 'Update',
        ];

        $url = "https://$this->domain/api/contents?type=Comic&page=$page&per_page=$display&orderBy=" . $sortOrder[$sortBy];
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['paginator']['last_page'] ? $page + 1 : '',
            'lists' => [],
        ];

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua'];

        foreach ($result['data'] as $list) {
            array_push($data['lists'], [
                'title' => $list['title'],
                'cover' => $list['cover'],
                'type' => $type[strtolower($list['country_id'])],
                'color' => $list['color'],
                'completed' => $list['status'] == 'completed' ? true : false,
                'chapter' => $list['lastChapters'][0]['number'],
                'date' => $list['lastChapters'][0]['updated_at']['time'],
                'url' => $this->toAbsoluteUrl('/comic/' . $list['slug'], $this->domain),
                'slug' => $list['slug'],
            ]);
        }
        return $data;
    }

    private function makeRequest($url, $options = [])
    {
        $response = Http::load($url, $options);
        // if (!$response->isSuccess() && $response->isBlocked()) $response = Http::bypass($url, $options);
        if (!$response->isSuccess() && $response->isBlocked()) $response = Http::proxy($url, $options);
        return $response;
    }

    private function toAbsoluteUrl($url, $domain = null)
    {
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        } elseif (strpos($url, '/') === 0) {
            return $domain ? "https://$domain$url" : "https://$url";
        } else {
            return $url;
        }
    }

    private function generateHeaders($url, $method = 'GET') {
        $secret =  $this->cryptojs_aes_decrypt( $this->signatureEnc,  $this->signaturePass);
        if (!$secret) {
            throw new Exception("AES decryption failed");
        }

        $now = time();
        $x = 'wm-api-request';
        $url_path = parse_url($url, PHP_URL_PATH);
        $payload = $now . $method . $url_path .  $this->accessKey . $secret;
        $signature =  $this->hmac_sha256($x, $payload);

        return [
            "Origin: https://$this->domain", //optional
            "Referer: https://$this->domain/", //optional
            "User-Agent: $this->userAgent", //optional
            'x-wm-request-time: ' . $now,
            'x-wm-accses-key: ' . $this->accessKey,
            'x-wm-request-signature: ' . $signature
        ];
    }

    private function cryptojs_aes_decrypt($ecnryptedStr, $passphrase) {
        $salted = base64_decode($ecnryptedStr);
        $salt = substr($salted, 8, 8);
        $ct = substr($salted, 16);

        // Generate key and IV using EVP_BytesToKey method
        $data = '';
        $key = '';
        while (strlen($key) < 48) { // 32 bytes key + 16 bytes IV = 48
            $data = md5($data . $passphrase . $salt, true);
            $key .= $data;
        }

        $aes_key = substr($key, 0, 32); // 256-bit key
        $aes_iv = substr($key, 32, 16); // 128-bit IV

        return openssl_decrypt($ct, 'aes-256-cbc', $aes_key, OPENSSL_RAW_DATA, $aes_iv);
    }

    private function hmac_sha256($data, $key) {
        return hash_hmac('sha256', $data, $key);
    }
}
