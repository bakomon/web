<?php

// Source: https://github.com/KotatsuApp/kotatsu-parsers/blob/6abcdd8d4bdb1d3463a2d1d47b9dbdcd182291ca/src/main/kotlin/org/koitharu/kotatsu/parsers/site/all/ComickFunParser.kt

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Services\Http;
use Faker\UserAgentGenerator;

class ComickParser
{
    public $response;
    private $headers;
    private $domain = 'comick.dev';
    private $coverDomain = 'meo.comick.pictures';
    private $languageCode = 'en';

    public function __construct()
    {
        $this->headers = [
            "Origin: https://$this->domain", //optional
            "Referer: https://$this->domain/",
            'User-Agent: ' . (new UserAgentGenerator)->userAgent(),
        ];
    }

    public function getChapter($params)
    {
        $slug = $params['slug'];
        $chapterID = $params['chapter_id'];

        $url = "https://api.$this->domain/chapter/$chapterID?tachiyomi=true";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/comic/$slug/$chapterID", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $chapter = $result['chapter'];

        if ($result['next']) {
            $next = [
                'number' => (string)$result['next']['chap'],
                'chapter_id' => (string)$result['next']['hid'],
            ];
        } else {
            $next = json_decode('{}');
        }

        if ($result['prev']) {
            $prev = [
                'number' => (string)$result['prev']['chap'],
                'chapter_id' => (string)$result['prev']['hid'],
            ];
        } else {
            $prev = json_decode('{}');
        }

        $img_lists = [];
        if (isset($chapter['images']) && is_array($chapter['images'])) {
            foreach ($chapter['images'] as $list) {
                array_push($img_lists, $list['url']);
            }
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

    private function fetchChapters($seriesID)
    {// series - chapter list
        $chapters_limit = '2147483647'; //int32 max value
        $url = "https://api.$this->domain/comic/$seriesID/chapters?limit=$chapters_limit";
        $response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($response->response(), true);

        $data = [];
        foreach ($result['chapters'] as $list) {
            if ($list['lang'] != $this->languageCode) continue;
            array_push($data, [
                'number' => (string)$list['chap'],
                'chapter_id' => (string)$list['hid'],
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

        $type = ['jp' => 'manga', 'kr' => 'manhwa', 'cn' => 'manhua'];
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
                'type' => $type[$series['country']] ?? '',
                'status' => $status[$series['status']],
                'released' => $series['year'],
                'author' => $this->getList($result['authors'], 'name'),
                'artist' => $this->getList($result['artists'], 'name'),
                'genre' => implode(', ', $genres),
            ],
            'desc' => preg_replace(['/\n+[\-_]+\n[\s\S]+/', '/\n+/'], ['', "\n"], $series['desc']),
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
            if ($list['content_rating'] == 'erotica') continue; //exclude_nsfw
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
            'library' => 'created_at', //added
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
