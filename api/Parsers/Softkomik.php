<?php

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use \DOMXpath;
use Api\Services\Http;
use Faker\UserAgentGenerator;

class SoftkomikParser
{
    public $response;
    private $userAgent;
    private $domain = 'softkomik.co';
    private $apiDomain = 'softdevices.my.id';
    private $coverDomain = 'cover.softdevices.my.id/softkomik-cover';
    private $chapterDomain = 'image.komik.im/softkomik';
    private $cookies = [];
    private $session;

    public function __construct()
    {
        $this->userAgent = (new UserAgentGenerator)->userAgent();
    }

    // https://github.com/keiyoushi/extensions-source/blob/e053a482c3404700e1944eab12f32b0f24af47d6/core/src/main/kotlin/keiyoushi/utils/NextJs.kt
    private function extractNextJs() {
        $xpath = new DOMXpath($this->response->responseParse());
        return $xpath->query('//script[@id="__NEXT_DATA__"]')->item(0)->textContent;
    }

    private function fetchImages($slug, $chapterNo, $chapterId)
    {// chapter - image list
        $url = "https://v2.$this->apiDomain/komik/$slug-bahasa-indonesia/chapter/$chapterNo/img/$chapterId";
        $response = $this->makeRequest($url, ['headers' =>$this->generateHeaders($url)]);

        $statusCode = (int)$response->getStatus();
        if ($this->isError($statusCode)) return [];

        $result = json_decode($response->response(), true);

        return $result['imageSrc'];
    }

    public function getChapter($params)
    {
        $slug = $params['slug'];
        $chapterNo = $params['chapter'];

        $url = "https://$this->domain/$slug-bahasa-indonesia/chapter/$chapterNo";
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);

        $statusCode = (int)$this->response->getStatus();
        if ($this->isError($statusCode)) return $this->showError($url, $statusCode);

        $result = json_decode($this->extractNextJs(), true);

        $chapter = $result['props']['pageProps']['data'];

        if (count($chapter['nextChapter']) > 0) {
            $next_arr = $chapter['nextChapter'][0]['chapter'];
            $next = [
                'number' => (string)$next_arr,
                'url' => "/$slug-bahasa-indonesia/chapter/$next_arr",
            ];
        } else {
            $next = json_decode('{}');
        }

        if (count($chapter['prevChapter']) > 0) {
            $prev_arr = $chapter['prevChapter'][0]['chapter'];
            $prev = [
                'number' => (string)$prev_arr,
                'url' => "/$slug-bahasa-indonesia/chapter/$prev_arr",
            ];
        } else {
            $prev = json_decode('{}');
        }

        $img_lists = [];
        $img_data = $chapter['data'];
        if (count($img_data['imageSrc']) == 0) $result['imageSrc'] = $this->fetchImages($slug, $chapterNo, $img_data['_id']);
        foreach ($result['imageSrc'] as $list) {
            $img = (strpos($list, '/') !== 0 ? '/' : '') . $list;
            array_push($img_lists, $this->toAbsoluteUrl($img, $this->chapterDomain));
        }

        return [
            'title' => $chapter['komik']['title'],
            'cover' => '',
            'current' => (string)$chapterNo,
            'next' => $next,
            'prev' => $prev,
            'source' => $url,
            'images' => $img_lists,
        ];
    }

    private function fetchChapters($slug)
    {// series - chapter list
        $chapters_limit = '2147483647'; //int32 max value
        $url = "https://v2.$this->apiDomain/komik/$slug-bahasa-indonesia/chapter?limit=$chapters_limit";
        $response = $this->makeRequest($url, ['headers' =>$this->generateHeaders($url)]);

        $statusCode = (int)$response->getStatus();
        if ($this->isError($statusCode)) return [];

        $result = json_decode($response->response(), true);

        $data = [];
        foreach ($result['chapter'] as $list) {
            array_push($data, [
                'number' => (string)$list['chapter'],
            ]);
        }
        return $data;
    }

    public function getSeries($slug)
    {
        $url = "https://$this->domain/$slug-bahasa-indonesia";
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);

        $statusCode = (int)$this->response->getStatus();
        if ($this->isError($statusCode)) return $this->showError($url, $statusCode);

        $result = json_decode($this->extractNextJs(), true);

        $series = $result['props']['pageProps']['data'];
        $cover = (strpos($series['gambar'], '/') !== 0 ? '/' : '') . $series['gambar'];
        $chapters = $this->fetchChapters($slug);

        return [
            'title' => $series['title'],
            'alternative' => $series['title_alt'],
            'cover' => $this->toAbsoluteUrl($cover, $this->coverDomain),
            'detail' => [
                'type' => $series['type'],
                'status' => $series['status'],
                'released' => $series['tahun'] && $series['tahun'] != '0' ? $series['tahun'] : '',
                'author' => $series['author'],
                'artist' => '',
                'genre' => implode(', ', $series['Genre']),
            ],
            'desc' => preg_replace('/\n+/', "\n", $series['sinopsis']),
            'source' => $url,
            'chapter' => $chapters,
        ];
    }

    public function getSearch($adv, $value, $page = 1, $display = 24)
    {
        // $url = "https://v3.$this->apiDomain/get/softkomik/v2/komik?page=$page&limit=$display&sortBy=newKomik";
        $url = "https://v2.$this->apiDomain/komik?page=$page&limit=$display";
        if (strpos($value, 'sortBy=') === FALSE) $url .= '&sortBy=newKomik';
        if (!$adv) $url .= '&search=true';
        $url .= '&' . ($adv ? $value : "name=$value");
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);

        $statusCode = (int)$this->response->getStatus();
        if ($this->isError($statusCode)) return $this->showError($url, $statusCode);

        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['maxPage'] ? $page + 1 : '',
            'lists' => [],
        ];

        foreach ($result['data'] as $list) {
            $cover = (strpos($list['gambar'], '/') !== 0 ? '/' : '') . $list['gambar'];
            $slug = preg_replace('/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i', '', $list['title_slug']);
            array_push($data['lists'], [
                'title' => $list['title'],
                'cover' => $this->toAbsoluteUrl($cover, $this->coverDomain),
                'type' => $list['type'],
                'color' => '',
                'completed' => $list['status'] == 'tamat',
                'url' => $this->toAbsoluteUrl('/' . $list['title_slug'], $this->domain),
                'slug' => $slug,
            ]);
        }
        return $data;
    }

    public function getLatest($sortBy, $page = 1, $display = 24)
    {
        $sortOrder = [
            'library' => 'newKomik', //added
            'update' => 'new',
        ];

        // $url = "https://v3.$this->apiDomain/get/softkomik/v2/komik?page=$page&limit=24&sortBy=" . $sortOrder[$sortBy];
        $url = "https://v2.$this->apiDomain/komik?page=$page&limit=$display&sortBy=" . $sortOrder[$sortBy];
        $this->response = $this->makeRequest($url, ['headers' => $this->generateHeaders($url)]);

        $statusCode = (int)$this->response->getStatus();
        if ($this->isError($statusCode)) return $this->showError($url, $statusCode);

        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['maxPage'] ? $page + 1 : '',
            'lists' => [],
        ];

        foreach ($result['data'] as $list) {
            $cover = (strpos($list['gambar'], '/') !== 0 ? '/' : '') . $list['gambar'];
            $slug = preg_replace('/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i', '', $list['title_slug']);
            array_push($data['lists'], [
                'title' => $list['title'],
                'cover' => $this->toAbsoluteUrl($cover, $this->coverDomain),
                'type' => $list['type'],
                'color' => '',
                'completed' => $list['status'] == 'tamat',
                'chapter' => (string)$list['latestChapter'],
                'date' => $list['updated_at'],
                'url' => $this->toAbsoluteUrl('/' . $list['title_slug'], $this->domain),
                'slug' => $slug,
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

    private function generateHeaders($url) {
        $session = $this->getSession();

        return [
            "Origin: https://$this->domain", //optional
            "Referer: https://$this->domain/", //optional
            "User-Agent: $this->userAgent", //optional
            'x-sign: ' . $session['sign'],
            'x-token: ' . $session['token'],
        ];
    }

    private function getCookie()
    {
        $result = $this->makeRequest("https://$this->domain");
        $cookieString = '';

        foreach ($result->headers as $key => $values) {
            if (strtolower(str_replace('_', '-', $key)) !== 'set-cookie') continue;
            foreach ((array)$values as $cookieHeader) {
                $pair = explode(';', $cookieHeader, 2)[0];
                if (strpos($pair, '=') === false) continue;
                if ($cookieString !== '') $cookieString .= '; ';
                $cookieString .= trim($pair);
            }
        }

        if ($cookieString !== '') $this->cookies = $cookieString;
        return $this->cookies;
    }

    // https://github.com/keiyoushi/extensions-source/blob/e053a482c3404700e1944eab12f32b0f24af47d6/src/id/softkomik/src/eu/kanade/tachiyomi/extension/id/softkomik/Softkomik.kt#L247
    private function getSession()
    {
        $nowMs = (int)(microtime(true) * 1000);
        if ($this->session !== null && $this->session['ex'] > $nowMs) {
            return $this->session;
        }

        $result = $this->makeRequest($this->toAbsoluteUrl('/api/sessions', $this->domain), ['headers' => ['Cookie: ' . $this->getCookie()]]);
        $this->session = json_decode($result->response(), true);
        return $this->session;
    }

    private function isError($statusCode)
    {
        if ($statusCode >= 200 && $statusCode < 300) {
            return false;
        }
        return true;
    }

    private function showError($url, $statusCode)
    {
        $errorMessage = 'UNKNOWN ERROR';

        if ($statusCode === 404) {
            $errorMessage = 'NOT FOUND';
        } elseif ($statusCode === 401) {
            $errorMessage = 'UNAUTHORIZED';
        }

        return [
            'error' => [
                'code' => $statusCode,
                'message' => $errorMessage,
            ],
            'source' => $url,
        ];
    }
}
