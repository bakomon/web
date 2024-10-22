<?php

/**
 * Source:
 * https://github.com/KotatsuApp/kotatsu-parsers/pull/273#issuecomment-1891094276
 * https://github.com/KotatsuApp/kotatsu-parsers/blob/b06288e7eb4fef4539324c779a0c814a3d735e4d/src/main/kotlin/org/koitharu/kotatsu/parsers/site/all/WebtoonsParser.kt
 */

namespace Api\Services;

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
        'User-Agent' => 'nApps (Android 12;; linewebtoon; 3.1.0)'
    ];

    public function __construct($source)
    {
        $this->locale = $source;
        $this->signer = new WebtoonsUrlSigner('gUtPzJFZch4ZyAGviiyH94P99lQ3pFdRTwpJWDlSGFfwgpr6ses5ALOxWHOIT7R1');
        $this->languageCode = $this->getLanguageCode($source);
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

    public function getChapter($titleNo, $episodeNo)
    {// getPages
        $url = "/lineWebtoon/webtoon/episodeInfo.json?v=4&titleNo=$titleNo&episodeNo=$episodeNo";
        $result = $this->makeRequest($url);
        if (isset($result['error'])) return $result;

        $chapter = $result['episodeInfo'];

        $img_lists = [];
        foreach ($chapter['imageInfo'] as $list) {
            array_push($img_lists, $this->toAbsoluteUrl($list['url'], $this->staticDomain));
        }

        return [
            'cover' => $this->toAbsoluteUrl($chapter['thumbnailImageUrl'], $this->staticDomain),
            'current' => (string)$episodeNo,
            'next' => isset($chapter['nextEpisodeNo']) && !$chapter['nextEpisodeRewardAd'] ? ['number' => (string)$chapter['nextEpisodeNo'] ] : json_decode('{}'),
            'prev' => isset($chapter['previousEpisodeNo']) ? ['number' => (string)$chapter['previousEpisodeNo'] ] : json_decode('{}'),
            'source' => /*$chapter['linkUrl']*/ '',
            'images' => $img_lists,
        ];
    }

    private function fetchEpisodes($titleNo)
    {// series - chapter list
        $url = "/lineWebtoon/webtoon/episodeList.json?v=5&titleNo=$titleNo";
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

    public function getSeries($titleNo)
    {// getDetails
        $url = "/lineWebtoon/webtoon/titleInfo.json?titleNo=$titleNo&anyServiceStatus=false";
        $result = $this->makeRequest($url);
        if (isset($result['error'])) return $result;

        $series = $result['titleInfo'];
        $chapters = $this->fetchEpisodes($titleNo);

        return [
            'title' => $series['title'],
            'alternative' => '',
            'cover' => $this->toAbsoluteUrl($series['thumbnail'], $this->staticDomain),
            'detail' => [
                'type' => 'webtoon',
                'status' => $series['restTerminationStatus'] == 'TERMINATION' ? 'Completed' : ($series['restTerminationStatus'] == 'REST' ? 'Hiatus' : 'Ongoing'),
                'author' => $series['writingAuthorName'],
                'artist' => $series['pictureAuthorName'],
                'genre' => $series['genreInfo']['name'],
            ],
            'desc' => preg_replace('/\n+/', "\x20", $series['synopsis']),
            // 'source' => $chapter['linkUrl'],
            'source' => "https://webtoons.com/$this->languageCode/originals/a/list?title_no=$titleNo",
            'chapter' => $chapters,
        ];
    }

    private function fetchDisplay($headers)
    {
        $url = "https://m.webtoons.com/$this->languageCode/search/result?searchType=WEBTOON&keyword=int32Max2147483647";
        $response = Http::load($url, ['headers' => $headers]);
        $display = json_decode($response->response(), true)['result']['webtoonResult']['display'];
        return $display;
    }

    public function getSearch($keyword, $page = 1)
    {
        $headers = [
            'Referer' => "https://m.webtoons.com/$this->languageCode/search",
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.186 Mobile Safari/537.36',
        ];

        $start = ($page - 1) * $this->fetchDisplay($headers) + 1;
        $url = "https://m.webtoons.com/$this->languageCode/search/result?searchType=WEBTOON&keyword=$keyword&start=$start";
        $this->response = Http::load($url, ['headers' => $headers]);
        $result = json_decode($this->response->response(), true)['result']['webtoonResult'];

        $search = $result['titleList'];
        if (count($search) == 0) return [];

        $data = [
            'display' => $result['display'],
            'total' => $result['total'],
            'list' => [],
        ];

        foreach ($search as $list) {
            if (preg_match('/\(webnovel\)/i', $list['title'])) continue;
            array_push($data['list'], [
                'title_no' => (string)$list['titleNo'],
                'title' => $list['title'],
                'cover' => $this->toAbsoluteUrl($list['thumbnailMobile'], $this->staticDomain),
                'type' => 'webtoon',
                'color' => '',
                'completed' => $list['restTerminationStatus'] == 'TERMINATION',
                'url' => $this->toAbsoluteUrl("/$this->languageCode/originals/a/list?title_no=" . $list['titleNo'], $this->domain),
                'slug' => $list['titleGroupName'],
            ]);
        }
        return $data;
    }

    public function getLatest($sortBy)
    {// getAllTitleList
        $url = '/lineWebtoon/webtoon/titleList.json?';
        $result = $this->makeRequest($url);
        if (isset($result['error'])) return $result;

        $sortOrder = [
            'added' => 'registerYmdt',
            'update' => 'lastEpisodeRegisterYmdt',
            'popular' => 'readCount',
            'subscribe' => 'favoriteCount',
            'rating' => 'starScoreAverage',
            'likeit' => 'likeitCount',
        ];

        $allTitle = $result['titleList']['titles'];
        if (count($allTitle) == 0) return [];
        $this->sortByKey($allTitle, $sortOrder[$sortBy], 'desc');

        $data = [];
        foreach ($allTitle as $list) {
            if ($list['webnovel'] == true) continue;
            array_push($data, [
                'title_no' => (string)$list['titleNo'],
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
        return count($data) == 0 ? [] : $data;
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
                    'url' => $finalUrl,
                ],
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
