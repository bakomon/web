<?php

// Source: https://github.com/KotatsuApp/kotatsu-parsers/blob/6abcdd8d4bdb1d3463a2d1d47b9dbdcd182291ca/src/main/kotlin/org/koitharu/kotatsu/parsers/site/all/ComickFunParser.kt

namespace Api\Parsers;

use Api\Services\Http;

class ComickParser
{
    public $response;
    private $headers;
    private $domain = 'comick.io';
    private $coverDomain = 'meo.comick.pictures';
    private $languageCode = 'en';
    private $user_agent = [ // mobile, https://explore.whatismybrowser.com/useragents/explore/software_name/
      'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.6943.49 Mobile Safari/537.36', //chrome
      'Mozilla/5.0 (Android 15; Mobile; rv:135.0) Gecko/135.0 Firefox/135.0', //firefox
      'Mozilla/5.0 (Linux; Android 11; EB2101) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Mobile Safari/537.36 Edg/96.0.1054.53', // edge
      'Mozilla/5.0 (Linux; U; Android 14; 23049PCD8G Build/UKQ1.230804.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/130.0.6723.108 Mobile Safari/537.36 OPR/79.0.2254.70768', //opera
      'Mozilla/5.0 (Linux; arm_64; Android 15; SM-G965F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.6943.49 YaBrowser/25.2.0.241 Mobile Safari/537.36', //yandex
      'Mozilla/5.0 (Linux; Android 10; BRAVE Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/131.0.6778.201 Mobile Safari/537.36 binu/8799 (be50ddc661fd7cca) Moya/7.4.0', //brave
      'Mozilla/5.0 (Linux; Android 10; SM-A013G Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/131.0.6778.200 Mobile Safari/537.36' //android-webview
    ];

    public function __construct()
    {
        $this->headers = [
            'Referer: https://comick.io/',
            'User-Agent: ' . $this->user_agent[array_rand($this->user_agent)],
        ];
    }

    public function getChapter($slug, $chapterID)
    {
        $url = "https://api.$this->domain/chapter/$chapterID?tachiyomi=true";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/comic/$slug/$chapterID", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $chapter = $result['chapter'];

        if ($result['next']) {
            $next = [
                'number' => (string)$result['next']['chap'],
                'chapterID' => (string)$result['next']['hid'],
            ];
        } else {
            $next = json_decode('{}');
        }

        if ($result['prev']) {
            $prev = [
                'number' => (string)$result['prev']['chap'],
                'chapterID' => (string)$result['prev']['hid'],
            ];
        } else {
            $prev = json_decode('{}');
        }

        $img_lists = [];
        foreach ($chapter['images'] as $list) {
            array_push($img_lists, $list['url']);
        }

        return [
            'title' => $chapter['md_comics']['title'],
            'cover' => $chapter['md_comics']['cover_url'],
            'current' => $chapter['chap'],
            'next' => $next,
            'prev' => $prev,
            'source' => $source,
            'images' => $img_lists,
        ];
    }

    private function fetchChapters($titleID)
    {// series - chapter list
        $chapters_limit = '2147483647'; //int32 max value
        $url = "https://api.$this->domain/comic/$titleID/chapters?limit=$chapters_limit";
        $response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($response->response(), true);

        $data = [];
        foreach ($result['chapters'] as $list) {
            if ($list['lang'] != $this->languageCode) continue;
            array_push($data, [
                'number' => (string)$list['chap'],
                'chapterID' => (string)$list['hid'],
            ]);
        }
        return $data;
    }

    public function getSeries($slug)
    {
        $url = "https://api.$this->domain/comic/$slug?tachiyomi=true";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/comic/$slug?lang=$this->languageCode", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua', 'others' => ''];
        $status = ['1' => 'ongoing', '2' => 'completed', '3' => 'canceled', '4' => 'hiatus'];

        $series = $result['comic'];
        $chapters = $this->fetchChapters($series['hid']);

        $genres = [];
        foreach ($series['md_comic_md_genres'] as $list) {
            array_push($genres, $list['md_genres']['name']);
        }

        return [
            'title' => $series['title'],
            'alternative' => $this->getList($series['md_titles'], 'title'),
            'cover' => $series['cover_url'],
            'detail' => [
                'type' => $type[$series['country']],
                'status' => $status[$series['status']],
                'released' => $series['year'],
                'author' => $this->getList($result['authors'], 'name'),
                'artist' => $this->getList($result['artists'], 'name'),
                'genre' => implode(', ', $genres),
            ],
            'desc' => preg_replace('/(\s+)?(\n+)?(\s+)?(\*+)?(notes?|links?):(\*+)?[\s\S]+/i', '', $series['desc']),
            'source' => $source,
            'chapter' => $chapters,
        ];
    }

    private function fetchNext($url, $page)
    {
        $next_page = (int)$page + 1;
        $next_link = preg_replace('/([\?&\/]page[=\/])\d+/', '${1}' . $next_page, $url);
        $next_xml = $this->makeRequest($next_link, ['headers' => $this->headers]);
        return (int)$next_xml->getStatus() >= 400 || $next_xml->response() == '[]' || $next_xml->response() == '' ? '' : (string)$next_page;
    }

    public function getSearch($adv, $value, $page = 1, $display = 24)
    {
        $exclude_nsfw = 'excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri';
        $url = "https://api.$this->domain/v1.0/search?$exclude_nsfw&type=comic&showall=true&page=$page&limit=$display&tachiyomi=true";
        $url .= '&' . ($adv ? $value : "q=$value");

        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $this->fetchNext($url, $page),
            'lists' => [],
        ];

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua'];

        foreach ($result as $list) {
            if ($list['content_rating'] !== 'safe') continue;
            array_push($data['lists'], [
                // 'series_id' => (string)$list['hid'],
                'title' => $list['title'],
                'cover' => $list['cover_url'],
                'type' => $type[$list['country']] ?? '',
                'color' => '',
                'completed' => $list['status'] == '2' ? true : false,
                'url' => $this->toAbsoluteUrl('/comic/' . $list['slug'], $this->domain),
                'slug' => $list['slug'],
            ]);
        }
        return $data;
    }

    public function getLatest($sortBy, $page = 1, $display = 24)
    {
        $sortOrder = [
            'added' => 'created_at',
            'update' => 'uploaded',
        ];

        $exclude_nsfw = 'excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri';
        $url = "https://api.$this->domain/v1.0/search?$exclude_nsfw&type=comic&showall=true&page=$page&limit=$display&sort=" . $sortOrder[$sortBy] . '&tachiyomi=true';
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($this->response->response(), true);

        /* !! ERROR: page more than 30
        {
            "statusCode": 400,
            "code": "FST_ERR_VALIDATION",
            "error": "Bad Request",
            "message": "querystring/page must be <= 30, querystring/page must be <= 5, querystring must match a schema in anyOf"
        }
        */

        $data = [
            'next' => $page < 30 ? $page + 1 : '',
            'lists' => [],
        ];
        if ($this->response->getStatus() != 200) return $data;

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua'];

        foreach ($result as $list) {
            if ($list['content_rating'] !== 'safe') continue;
            array_push($data['lists'], [
                // 'series_id' => (string)$list['hid'],
                'title' => $list['title'],
                'cover' => $list['cover_url'],
                'type' => $type[$list['country']] ?? '',
                'color' => '',
                'completed' => $list['status'] == '2' ? true : false,
                'chapter' => (string)$list['last_chapter'],
                'date' => $list['uploaded_at'],
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

    private function getList($data, $name)
    {
        $arr = [];
        foreach ($data as $list) {
            array_push($arr, $list[$name]);
        }
        return implode(', ', $arr);
    }
}
