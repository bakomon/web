<?php

/**
 * Source:
 * https://github.com/KotatsuApp/kotatsu-parsers/pull/273#issuecomment-1891094276
 * https://github.com/KotatsuApp/kotatsu-parsers/blob/b06288e7eb4fef4539324c779a0c814a3d735e4d/src/main/kotlin/org/koitharu/kotatsu/parsers/site/all/WebtoonsParser.kt
 */

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Services\Http;
use Faker\UserAgentGenerator;

class WebtoonsParser
{
    public $response;
    private $locale;
    private $signer;
    private $domain = 'webtoons.com';
    private $apiDomain = 'global.apis.naver.com';
    private $staticDomain = 'webtoon-phinf.pstatic.net';
    private $languageCode;
    private $headers = [
        'User-Agent' => 'nApps (Android 14;; linewebtoon; 3.7.0)'
    ];

    public function __construct($lang)
    {
        $this->locale = $lang;
        $this->signer = new WebtoonsUrlSigner('gUtPzJFZch4ZyAGviiyH94P99lQ3pFdRTwpJWDlSGFfwgpr6ses5ALOxWHOIT7R1');
        $this->languageCode = $this->getLanguageCode($lang);
    }

    private function getLanguageCode($locale)
    {
        switch ($locale) {
            case 'in':
                return 'id';
            case 'zh':
                return 'zh-hant';
            default:
                return $locale;
        }
    }

    private function filterByKeyValue($array, $key, $query) {
        return array_filter($array, function($list) use ($key, $query) {
            return isset($list[$key]) && preg_match("/$query/i", $list[$key]);
        });
    }

    private function sortByKey(&$array, $key, $order = 'asc') {
        usort($array, function($a, $b) use ($key, $order) {
            if (!isset($a[$key]) || !isset($b[$key])) {
                return 0;
            }
            if ($order == 'asc') {
                return $a[$key] <=> $b[$key];
            } else {
                return $b[$key] <=> $a[$key];
            }
        });
    }

    public function getChapter($params)
    {// getPages
        $seriesID = $params['series_id'];
        $chapterNo = $params['chapter'];

        $url = "/lineWebtoon/webtoon/episodeInfo.json?v=4&titleNo=$seriesID&episodeNo=$chapterNo";
        $result = $this->makeRequest($url);

        $source = "https://webtoons.com/$this->languageCode/originals/a/e/viewer?title_no=$seriesID&episode_no=$chapterNo";
        if (isset($result['error'])) {
            $result['source'] = $source;
            return $result;
        }

        $chapter = $result['episodeInfo'];

        $img_lists = [];
        foreach ($chapter['imageInfo'] as $list) {
            array_push($img_lists, $this->toAbsoluteUrl($list['url'], $this->staticDomain));
        }

        return [
            'title' => '',
            'cover' => $this->toAbsoluteUrl($chapter['thumbnailImageUrl'], $this->staticDomain),
            'current' => (string)$chapterNo,
            'next' => isset($chapter['nextEpisodeNo']) && !$chapter['nextEpisodeRewardAd'] ? [ 'number' => (string)$chapter['nextEpisodeNo'] ] : json_decode('{}'),
            'prev' => isset($chapter['previousEpisodeNo']) ? [ 'number' => (string)$chapter['previousEpisodeNo'] ] : json_decode('{}'),
            // 'source' => $chapter['linkUrl'],
            'source' => $source,
            'images' => $img_lists,
        ];
    }

    private function fetchEpisodes($seriesID)
    {// series - chapter list
        $url = "/lineWebtoon/webtoon/episodeList.json?v=7&titleNo=$seriesID";
        $result = $this->makeRequest($url)['episodeList']['episode'];

        $data = [];
        foreach ($result as $list) {
            if (!preg_match('/service/i', $list['serviceStatus'])) continue;
            array_push($data, [
                'number' => (string)$list['episodeNo'],
            ]);
        }
        return $data;
    }

    public function getSeries($seriesID)
    {// getDetails
        $url = "/lineWebtoon/webtoon/titleInfo.json?titleNo=$seriesID&anyServiceStatus=false";
        $result = $this->makeRequest($url);

        $source = "https://webtoons.com/$this->languageCode/originals/a/list?title_no=$seriesID";
        if (isset($result['error'])) {
            $result['source'] = $source;
            return $result;
        }

        $series = $result['titleInfo'];
        $chapters = $this->fetchEpisodes($seriesID);

        return [
            'title' => $series['title'],
            'alternative' => '',
            'cover' => $this->toAbsoluteUrl($series['thumbnail'], $this->staticDomain),
            'detail' => [
                'type' => 'webtoon',
                'status' => $series['restTerminationStatus'] == 'TERMINATION' ? 'Completed' : ($series['restTerminationStatus'] == 'REST' ? 'Hiatus' : 'Ongoing'),
                'released' => '',
                'author' => $series['writingAuthorName'],
                'artist' => $series['pictureAuthorName'],
                'genre' => $series['genreInfo']['name'],
            ],
            'desc' => preg_replace('/\n+/', "\n", $series['synopsis']),
            // 'source' => $series['linkUrl'],
            'source' => $source,
            'chapter' => $chapters,
        ];
    }

    public function getSearch($adv, $keyword, $page = 1, $display = 30)
    {
        $start = ($page - 1) * $display + 1;
        $url = "/lineWebtoon/webtoon/searchWebtoon?query=$keyword&startIndex=$start&pageSize=$display"; // "/searchAll", "/searchWebtoon", "/searchChallenge"
        $result = $this->makeRequest($url);
        if (isset($result['error'])) return $result;

        $search = $result['webtoonSearch']['titleList'];

        $data = [
            'next' => '',
            'lists' => [],
        ];
        if (count($search) == 0) return $data;

        foreach ($search as $list) {
            if (preg_match('/\(webnovel\)/i', $list['title'])) continue;
            $slug = preg_replace('/[\s\-_]+/', '-', strtolower(preg_replace('/[^\w\s\-_]/', '', $list['title'])));
            array_push($data['lists'], [
                'series_id' => (string)$list['titleNo'],
                'title' => $list['title'],
                'cover' => $this->toAbsoluteUrl($list['thumbnail'], $this->staticDomain),
                'type' => 'webtoon',
                'color' => '',
                'completed' => '',
                'url' => $this->toAbsoluteUrl("/$this->languageCode/originals/a/list?title_no=" . $list['titleNo'], $this->domain),
                'slug' => $slug,
            ]);
        }

        $totalPages = ceil($result['webtoonSearch']['total'] / $display);
        $next_page = (int)$page + 1;
        $data['next'] = $totalPages >= $next_page ? (string)$next_page : '';

        return $data;
    }

    public function getLatest($sortBy, $page = 1, $display = 24)
    {// getAllTitleList
        $sortOrder = [
            'library' => 'registerYmdt', //added
            'update' => 'lastEpisodeRegisterYmdt',
            // 'popular' => 'readCount',
            // 'subscribe' => 'favoriteCount',
            // 'rating' => 'starScoreAverage',
            // 'likeit' => 'likeitCount',
        ];

        $url = '/lineWebtoon/webtoon/titleList.json?v=3';
        $result = $this->makeRequest($url);
        if (isset($result['error'])) return $result;

        $allTitle = $result['titleList']['titles'];

        $data = [
            'next' => '',
            'lists' => [],
        ];
        if (count($allTitle) == 0) return $data;

        $this->sortByKey($allTitle, $sortOrder[$sortBy], 'desc');
        $allList = [];
        foreach ($allTitle as $list) {
            if ($list['webnovel'] == true) continue;
            array_push($allList, [
                'series_id' => (string)$list['titleNo'],
                'title' => $list['title'],
                'cover' => $this->toAbsoluteUrl($list['thumbnail'], $this->staticDomain),
                'type' => 'webtoon',
                'color' => '',
                'completed' => $list['restTerminationStatus'] == 'TERMINATION',
                'chapter' => '',
                'date' => '',
                'url' => $this->toAbsoluteUrl("/$this->languageCode/originals/a/list?title_no=" . $list['titleNo'], $this->domain),
                'slug' => $list['groupName'],
            ]);
        }

        $chunks = array_chunk($allList, $display); //split array
        $data['lists'] = $chunks[(int)$page - 1] ?? [];

        $next_page = (int)$page + 1;
        $data['next'] = isset($chunks[$next_page]) ? (string)$next_page : '';

        return $data;
    }

    private function makeRequest($url)
    {
        $finalUrl = $this->finalizeUrl($url);
        $this->response = Http::load($finalUrl, ['headers' => $this->headers]);
        $body = json_decode($this->response->response(), true);

        if (isset($body['message']['result']) && $this->response->status >= 200 && $this->response->status < 300) {
            return $body['message']['result'];
        } else {
            if ($this->response->status == 404) {
                $code = 404;
                $message = 'NOT FOUND';
            } else {
                $code = $body['message']['code'] ?? $body['error_code'];
                $message = $body['message']['message'] ?? $body['message'];
            }
            return [
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
                'source' => $finalUrl,
            ];
        }
    }

    private function finalizeUrl($url)
    {
        $url = $this->toAbsoluteUrl($url, $this->apiDomain);
        $urlComponents = parse_url($url);

        $queryParams = [];
        if (isset($urlComponents['query'])) parse_str($urlComponents['query'], $queryParams);

        $queryParams['serviceZone'] = 'GLOBAL';
        if (!isset($queryParams['v'])) $queryParams['v'] = '1';
        $queryParams['language'] = $this->languageCode;
        $queryParams['locale'] = $this->languageCode;
        $queryParams['platform'] = 'APP_ANDROID';
        $urlComponents['query'] = http_build_query($queryParams);

        $urlComponents['query'] .= $this->signer->makeEncryptUrl($this->buildUrl($urlComponents));
        $url = $this->buildUrl($urlComponents);
        return $url;
    }

    private function toAbsoluteUrl($url, $domain)
    {
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        } elseif (strpos($url, '/') === 0) {
            return "https://$domain$url";
        } else {
            return $url;
        }
    }

    private function buildUrl(array $components) {
        return (isset($components['scheme']) ? "{$components['scheme']}:" : '') .
            ((isset($components['user']) || isset($components['host'])) ? '//' : '') .
            (isset($components['user']) ? "{$components['user']}" : '') .
            (isset($components['pass']) ? ":{$components['pass']}" : '') .
            (isset($components['user']) ? '@' : '') .
            (isset($components['host']) ? "{$components['host']}" : '') .
            (isset($components['port']) ? ":{$components['port']}" : '') .
            (isset($components['path']) ? "{$components['path']}" : '') .
            (isset($components['query']) ? "?{$components['query']}" : '') .
            (isset($components['fragment']) ? "#{$components['fragment']}" : '');
    }
}

class WebtoonsUrlSigner {

    protected $secret;
    protected $mac;

    public function __construct($secret) {
        $this->secret = $secret;
        $this->mac = hash_hmac('sha1', '', $secret, true);
    }

    protected function getMessage($url, $msgpad) {
        $url = substr($url, 0, min(255, strlen($url)));
        return $url . $msgpad;
    }

    protected function getMessageDigest($message) {
        $hmac = hash_hmac('sha1', $message, $this->secret, true);
        return base64_encode($hmac);
    }

    public function makeEncryptUrl($url) {
        $msgPad = sprintf('%.0f', microtime(true) * 1000);
        $digest = $this->getMessageDigest($this->getMessage($url, $msgPad));
        return "&msgpad=$msgPad&md=" . urlencode($digest);
    }
}
