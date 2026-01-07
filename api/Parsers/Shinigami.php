<?php

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Services\Http;
use Faker\UserAgentGenerator;

class ShinigamiParser
{
    public $response;
    private $headers;
    private $domain = '07.shinigami.asia';
    private $apiDomain = 'api.shngm.io';
    // private $cdnDomain = 'storage.shngm.id'; //base_url
    private $cdnDomain = 'delivery.shngm.id'; //base_url

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
        $chapterID = $params['chapter_id'];
        $url = "https://$this->apiDomain/v1/chapter/detail/$chapterID";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/chapter/$chapterID", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $chapter = $result['data'];

        if ($chapter['next_chapter_id']) {
            $next = [
                'number' => (string)$chapter['next_chapter_number'],
                'chapter_id' => (string)$chapter['next_chapter_id'],
            ];
        } else {
            $next = json_decode('{}');
        }

        if ($chapter['prev_chapter_id']) {
            $prev = [
                'number' => (string)$chapter['prev_chapter_number'],
                'chapter_id' => (string)$chapter['prev_chapter_id'],
            ];
        } else {
            $prev = json_decode('{}');
        }

        $img_lists = [];
        $images = $chapter['chapter'];
        foreach ($images['data'] as $list) {
            $img = $images['path'] . $list;
            array_push($img_lists, $this->toAbsoluteUrl($img, $this->cdnDomain));
        }

        return [
            'title' => '',
            'cover' => '',
            'current' => (string)$chapter['chapter_number'],
            'next' => $next,
            'prev' => $prev,
            'source' => $source,
            'images' => $img_lists,
        ];
    }

    private function fetchChapters($seriesID)
    {// series - chapter list
        $chapters_limit = '2147483647'; //int32 max value
        $url = "https://$this->apiDomain/v1/chapter/$seriesID/list?page=1&page_size=$chapters_limit&sort_by=chapter_number&sort_order=desc";
        $response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($response->response(), true);

        $data = [];
        foreach ($result['data'] as $list) {
            array_push($data, [
                'number' => (string)$list['chapter_number'],
                'chapter_id' => $list['chapter_id'],
            ]);
        }
        return $data;
    }

    public function getSeries($seriesID)
    {
        $url = "https://$this->apiDomain/v1/manga/detail/$seriesID";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/series/$seriesID", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $status = ['1' => 'ongoing', '2' => 'completed', '3' => 'hiatus'];
        $series = $result['data'];
        $chapters = $this->fetchChapters($seriesID);

        return [
            'title' => $series['title'],
            'alternative' => $series['alternative_title'],
            'cover' =>  $series['cover_image_url'],
            'detail' => [
                'type' => $series['taxonomy']['Format'][0]['slug'],
                'status' => $status[$series['status']],
                'released' => $series['release_year'],
                'author' => $this->getList($series['taxonomy']['Author'], 'name'),
                'artist' => isset($series['taxonomy']['Artist']) ? $this->getList($series['taxonomy']['Artist'], 'name') : '',
                'genre' => $this->getList($series['taxonomy']['Genre'], 'name'),
            ],
            'desc' => preg_replace('/\n+/', "\n", $series['description']),
            'source' => $source,
            'chapter' => $chapters,
        ];
    }

    public function getSearch($adv, $value, $page = 1, $display = 24)
    {
        $url = "https://$this->apiDomain/v1/manga/list?page=$page&page_size=$display&sort_order=desc";
        if (strpos($value, 'sort=') === FALSE) $url .= '&sort=latest';
        $url .= '&' . ($adv ? $value : "q=$value");

        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['meta']['total_page'] ? $page + 1 : '',
            'lists' => [],
        ];

        foreach ($result['data'] as $list) {
            $slug = preg_replace('/[\s\-_]+/', '-', strtolower(preg_replace('/[^\w\s\-_]/', '', $list['title'])));
            array_push($data['lists'], [
                'series_id' => $list['manga_id'],
                'title' => $list['title'],
                'cover' => $list['cover_image_url'],
                'type' => $list['taxonomy']['Format'][0]['slug'],
                'color' => '',
                'completed' => $list['status'] == '2' ? true : false,
                'url' => $this->toAbsoluteUrl('/series/' . $list['manga_id'], $this->domain),
                'slug' => $slug,
            ]);
        }
        return $data;
    }

    public function getLatest($sortBy, $page = 1, $display = 24)
    {
        $sortOrder = [
            'library' => 'bookmark',
            'update' => 'latest',
        ];
        $sortBy = isset($sortOrder[$sortBy]) ? $sortBy : 'update';

        $url = "https://$this->apiDomain/v1/manga/list?page=$page&page_size=$display&sort_order=desc&sort=" . $sortOrder[$sortBy];
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['meta']['total_page'] ? $page + 1 : '',
            'lists' => [],
        ];

        foreach ($result['data'] as $list) {
            $slug = preg_replace('/[\s\-_]+/', '-', strtolower(preg_replace('/[^\w\s\-_]/', '', $list['title'])));
            array_push($data['lists'], [
                'series_id' => $list['manga_id'],
                'title' => $list['title'],
                'cover' => $list['cover_image_url'],
                'type' => $list['taxonomy']['Format'][0]['slug'],
                'color' => '',
                'completed' => $list['status'] == '2' ? true : false,
                'chapter' => (string)$list['latest_chapter_number'],
                'date' => $list['updated_at'],
                'url' => $this->toAbsoluteUrl('/series/' . $list['manga_id'], $this->domain),
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

    private function getList($data, $name)
    {
        $arr = [];
        foreach ($data as $list) {
            array_push($arr, $list[$name]);
        }
        return implode(', ', $arr);
    }
}
