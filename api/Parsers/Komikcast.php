<?php

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Services\Http;
use Faker\UserAgentGenerator;

class KomikCastParser
{
    public $response;
    private $headers;
    private $domain = 'v2.komikcast.fit';
    private $apiDomain = 'be.komikcast.cc';

    public function __construct()
    {
        $this->headers = [
            "Origin: https://$this->domain",
            "Referer: https://$this->domain/",
            'User-Agent: ' . (new UserAgentGenerator)->userAgent(), //optional
        ];
    }

    public function getChapter($params)
    {
        $slug = $params['slug'];
        $chapterNo = $params['chapter'];

        $url = "https://$this->apiDomain/series/$slug/chapters/$chapterNo";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/series/$slug/chapter/$chapterNo", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $chapter = $result['data'];
        $next = json_decode('{}');
        $prev = json_decode('{}');

        $ch_lists = $this->fetchChapters($slug);
        foreach ($ch_lists as $index => $ch) {
            if ($ch['number'] == (string)$chapterNo) {
                if (isset($ch_lists[$index - 1])) {
                    $next = [
                        'number' => $ch_lists[$index - 1]['number'],
                    ];
                }
                if (isset($ch_lists[$index + 1])) {
                    $prev = [
                        'number' => $ch_lists[$index + 1]['number'],
                    ];
                }
                break;
            }
        }

        $cdn_rgx = '/(cdn|image)\.komikcast\.(com|me|xyz)/';
        $kc_cdn = ['sv1.imgkc1.my.id', 'sv2.imgkc2.my.id', 'sv3.imgkc3.my.id'];
        $img_lists = [];
        foreach ($chapter['data']['images'] as $list) {
            if (preg_match($cdn_rgx, $list)) {
                $cdn = $kc_cdn[array_rand($kc_cdn)];
                $list = preg_replace($cdn_rgx, $cdn, $list);
            }
            array_push($img_lists, $list);
        }

        return [
            'title' => '',
            'cover' => '',
            'current' => (string)$chapterNo,
            'next' => $next,
            'prev' => $prev,
            'source' => $source,
            'images' => $img_lists,
        ];
    }

    private function fetchChapters($slug)
    {// series - chapter list
        $url = "https://$this->apiDomain/series/$slug/chapters";
        $response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($response->response(), true);

        $data = [];
        foreach ($result['data'] as $list) {
            array_push($data, [
                'number' => (string)$list['data']['index'],
            ]);
        }
        return $data;
    }

    public function getSeries($slug)
    {
        $url = "https://$this->apiDomain/series/$slug?includeMeta=true";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);

        $source = $this->toAbsoluteUrl("/series/$slug", $this->domain);
        if ((int)$this->response->getStatus() == 404) return [ 'error' => [ 'code' => 404, 'message' => 'NOT FOUND' ], 'source' => $source ];

        $result = json_decode($this->response->response(), true);

        $series = $result['data']['data'];
        $chapters = $this->fetchChapters($slug);

        $genres = [];
        foreach ($series['genres'] as $list) {
            array_push($genres, $list['data']['name']);
        }

        return [
            'title' => $series['title'],
            'alternative' => $series['nativeTitle'] != '-' ? $series['nativeTitle'] : '',
            'cover' => $series['coverImage'],
            'detail' => [
                'type' => $series['format'],
                'status' => $series['status'],
                'released' => $series['releaseDate'] == '-' ? '' : $series['releaseDate'],
                'author' => $series['author'] == '-' ? '' : $series['author'],
                'artist' => '',
                'genre' => implode(', ', $genres),
            ],
            'desc' => preg_replace('/\n+/', "\n", htmlspecialchars($series['synopsis'], ENT_SUBSTITUTE)),
            'source' => $source,
            'chapter' => $chapters,
        ];
    }

    public function getSearch($adv, $value, $page = 1, $display = 24)
    {
        if ($adv && strpos($value, 'genres=') !== false) {
            preg_match('/genres=([^&]*)/', $value, $matches);
            $genres = explode('%2C', $matches[1]);
            $genreIds = [];
            foreach ($genres as $genre) {
                $genreIds[] = "genreIds=$genre";
            }
            $value = preg_replace('/[\?&]?genres=[^&]+/', '', $value);
            $value .= ($value ? '&' : '') . implode('&', $genreIds);
        }
        $url = "https://$this->apiDomain/series?takeChapter=1&includeMeta=true&take=$display&page=$page";
        $url .= '&' . ($adv ? $value : "title=$value");

        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['meta']['lastPage'] ? $page + 1 : '',
            'lists' => [],
        ];

        foreach ($result['data'] as $list) {
            $info = $list['data'];
            array_push($data['lists'], [
                'title' => $info['title'],
                'cover' => $info['coverImage'],
                'type' => $info['format'],
                'color' => '',
                'completed' => $info['status'] == 'completed' ? true : false,
                'url' => $this->toAbsoluteUrl('/series/' . $info['slug'], $this->domain),
                'slug' => $info['slug'],
            ]);
        }
        return $data;
    }

    public function getLatest($sortBy, $page = 1, $display = 24)
    {
        $sortOrder = [
            'library' => '',
            'update' => 'latest',
        ];

        $url = "https://$this->apiDomain/series?takeChapter=1&includeMeta=true&take=$display&page=$page";
        if (!empty($sortOrder[$sortBy])) {
            $url .= '&sort=' . $sortOrder[$sortBy];
        }
        $this->response = $this->makeRequest($url, ['headers' => $this->headers]);
        $result = json_decode($this->response->response(), true);

        $data = [
            'next' => $page < $result['meta']['lastPage'] ? $page + 1 : '',
            'lists' => [],
        ];

        foreach ($result['data'] as $list) {
            $info = $list['data'];
            array_push($data['lists'], [
                'title' => $info['title'],
                'cover' => $info['coverImage'],
                'type' => $info['format'],
                'color' => '',
                'completed' => $info['status'] == 'completed' ? true : false,
                'chapter' => (string)$list['chapters'][0]['chapterIndex'],
                'date' => $list['chapters'][0]['updatedAt'],
                'url' => $this->toAbsoluteUrl('/series/' . $info['slug'], $this->domain),
                'slug' => $info['slug'],
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
