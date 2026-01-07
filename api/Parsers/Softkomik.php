<?php

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Services\Http;
use Faker\UserAgentGenerator;

class SoftkomikParser
{
    public $response;
    private $headers;
    private $domain = 'softkomik.com';
    private $coverDomain = 'cover.softdevices.my.id/softkomik-cover';
    private $chapterDomain = 'image.softkomik.com/softkomik';

    public function __construct()
    {
        $this->headers = [ //optional
            "Origin: https://$this->domain",
            "Referer: https://$this->domain/",
            'User-Agent: ' . (new UserAgentGenerator)->userAgent(),
        ];
    }

    public function getChapter($params)
    {
        $slug = $params['slug'];
        $chapterNo = $params['chapter'];

        $url = "https://p.$this->domain/komik/$slug-bahasa-indonesia/$chapterNo";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/$slug-bahasa-indonesia/chapter/$chapterNo", $this->domain);
        if (preg_match('/cannot\sread/i', $this->response->response())) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $chapter = $result['komik'];
        $cover = (strpos($chapter['gambar'], '/') !== 0 ? '/' : '') . $chapter['gambar'];

        if (count($result['nextData']) > 0) {
            $next_str = $result['nextData'][0]['chapter'];
            $next = [
                'number' => (string)$next_str,
                'url' => "/$slug-bahasa-indonesia/chapter/$next_str",
            ];
        } else {
            $next = json_decode('{}');
        }

        if (count($result['prevData']) > 0) {
            $prev_str = $result['prevData'][0]['chapter'];
            $prev = [
                'number' => (string)$prev_str,
                'url' => "/$slug-bahasa-indonesia/chapter/$prev_str",
            ];
        } else {
            $prev = json_decode('{}');
        }

        $img_lists = [];
        foreach ($result['imgSrc'] as $list) {
            $img = (strpos($list, '/') !== 0 ? '/' : '') . $list;
            array_push($img_lists, $this->toAbsoluteUrl($img, $this->chapterDomain));
        }

        return [
            'title' => $chapter['title'],
            'cover' => $this->toAbsoluteUrl($cover, $this->coverDomain),
            'current' => (string)$chapterNo,
            'next' => $next,
            'prev' => $prev,
            'source' => $source,
            'images' => $img_lists,
        ];
    }

    public function getSeries($slug)
    {
        $url = "https://p.$this->domain/komik/$slug-bahasa-indonesia";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/$slug-bahasa-indonesia", $this->domain);
        if (preg_match('/no\skomik/i', $this->response->response())) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $cover = (strpos($result['imageCover'], '/') !== 0 ? '/' : '') . $result['imageCover'];
        $series = $result['DataKomik'];

        $genres = [];
        foreach ($result['Genre'] as $list) {
            array_push($genres, $list['nama_genre']);
        }

        $chapters = [];
        foreach ($result['DataChapter'] as $list) {
            array_push($chapters, [
                'number' => (string)$list['chapter'],
                'url' => '/' . $series['title_slug'] . '/chapter/' . $list['chapter'],
            ]);
        }

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
                'genre' => implode(', ', $genres),
            ],
            'desc' => preg_replace('/\n+/', "\n", $series['sinopsis']),
            'source' => $source,
            'chapter' => $chapters,
        ];
    }

    public function getSearch($adv, $value, $page = 1, $display = 24)
    {
        // $url = "https://v3.$this->domain/get/softkomik/v2/komik?page=$page&limit=$display&sortBy=newKomik";
        $url = "https://v2.$this->domain/komik?page=$page&limit=$display";
        if (strpos($value, 'sortBy=') === FALSE) $url .= '&sortBy=newKomik';
        if (!$adv) $url .= '&search=true';
        $url .= '&' . ($adv ? $value : "name=$value");

        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
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

        // $url = "https://v3.$this->domain/get/softkomik/v2/komik?page=$page&limit=24&sortBy=" . $sortOrder[$sortBy];
        $url = "https://v2.$this->domain/komik?page=$page&limit=$display&sortBy=" . $sortOrder[$sortBy];
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
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
}
