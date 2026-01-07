<?php

// Source: https://github.com/KotatsuApp/kotatsu-parsers/blob/02ac1cb896a5029a71873606a0e7fd59c0163cc4/src/main/kotlin/org/koitharu/kotatsu/parsers/site/id/Ikiru.kt

namespace Api\Parsers;

require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use \DOMXpath;
use Api\Services\Http;
use Faker\UserAgentGenerator;

class IkiruParser
{
    public $response;
    private $headers;
    private $domain = '02.ikiru.wtf';
    private $nonce = null;

    public function __construct()
    {
        $this->headers = [ //optional
            "Origin: https://$this->domain",
            "Referer: https://$this->domain/",
            'User-Agent: ' . (new UserAgentGenerator)->userAgent(),
        ];
    }

    public function getSearch($adv, $value, $page = 1, $display = 24)
    {

        $data = [
            'action' => 'advanced_search',
            'nonce' => $this->getNonce(),
            'genre_exclude' => [ //exclude_nsfw
                'crossdressing', 'gender-bender', 'genderswap',
                'incest', 'nsfw', 'shoujo-ai', 'shounen-ai', 'bodyswap'
            ],
            'page' => $page,
            'order' => 'desc',
            'query' => '',
            'status' => [],
            'type' => [],
            'orderby' => 'popular',
            'genre' => []
        ];

        if ($adv) {
            $keys = [
                'search_term' => 'query',
                'the_status' => 'status',
                'the_type' => 'type',
                'the_genre' => 'genre'
            ];

            parse_str($value, $parsed); //parse $value string into array
            foreach ($parsed as $k => $v) {
                if (isset($keys[$k])) {
                    // If value is comma-separated, convert to array
                    if (in_array($keys[$k], ['status', 'type', 'genre'])) {
                        $data[$keys[$k]] = explode(',', str_replace('%2C', ',', $v));
                    } else {
                        $data[$keys[$k]] = $v;
                    }
                } else {
                    $data[$k] = $v;
                }
            }
        } else {
            $data['query'] = $value;
        }

        $data['query'] = str_replace(['+', '%20'], ' ', $data['query']);
        foreach ($data as $key => $val) {
            if (is_array($val)) $data[$key] = json_encode($val);
        }

        // unique id for storing cache
        $index_data = $data;
        unset($index_data['nonce']);
        $index = md5(json_encode($index_data));

        $url = "https://$this->domain/wp-admin/admin-ajax.php?index=$index";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers, 'method' => 'POST', 'fields' => $data]);
        return $this->response;
    }

    public function getLatest($sortBy, $page = 1, $display = 24)
    {
        $sortOrder = [
            'library' => 'bookmarked',
            'update' => 'updated',
        ];

        $data = [
            'action' => 'advanced_search',
            'nonce' => $this->getNonce(),
            'genre_exclude' => [ //exclude_nsfw
                'crossdressing', 'gender-bender', 'genderswap',
                'incest', 'nsfw', 'shoujo-ai', 'shounen-ai', 'bodyswap'
            ],
            'page' => $page,
            'order' => 'desc',
            'orderby' => $sortOrder[$sortBy]
        ];

        // unique id for storing cache
        $index_data = $data;
        unset($index_data['nonce']);
        $index = md5(json_encode($index_data));

        $url = "https://$this->domain/wp-admin/admin-ajax.php?index=$index";
        $this->response = $this->makeRequest($url, ['headers' => $this->headers, 'method' => 'POST', 'fields' => $data]);
        return $this->response;
    }

    private function makeRequest($url, $options = [])
    {
        $response = Http::load($url, $options);
        // if (!$response->isSuccess() && $response->isBlocked()) $response = Http::bypass($url, $options);
        if (!$response->isSuccess() && $response->isBlocked()) $response = Http::proxy($url, $options);
        return $response;
    }

    private function parseHtml($html)
    {
        $doc = $html->responseParse();
        return new DOMXPath($doc);
    }

    private function getNonce()
    {
        if ($this->nonce === null) {
            $url = "https://{$this->domain}/wp-admin/admin-ajax.php?type=search_form&action=get_nonce";
            $html = $this->makeRequest($url, ['headers' => $this->headers]);
            $xpath = $this->parseHtml($html);
            $nonce = $xpath->query("//input[@name='search_nonce']/@value");
            $this->nonce = $nonce->length ? $nonce->item(0)->nodeValue : '';
        }
        return $this->nonce ?? '';
    }
}
