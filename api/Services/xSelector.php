<?php

// XPath

namespace Api\Services;

class xSelector
{
    public static $source_default = 'ikiru';

    public static $source_lists = ['bacakomik', 'tukangkomik', 'ikiru', 'maid', 'komiksin', 'kiryuu', 'komikcast', 'pojokmanga', 'klikmanga', 'komiknesia', 'komiklovers', 'webtoons', 'komiku', 'mgkomik', 'shinigami', 'leviatanscans', 'reaper_scans', 'manhuaus'];

    public static function bacakomik()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'eastheme',
            'lang' => 'id',
            'url' => [
                'host' => 'https://bacakomik.net',
                'latest' => 'https://bacakomik.net/komik-terbaru/page/{$page}/',
                'search' => 'https://bacakomik.net/page/{$page}/?s={$value}',
                'advanced' => 'https://bacakomik.net/daftar-komik/page/{$page}/{$value}',
                'series' => 'https://bacakomik.net/komik/{$slug}/',
                'chapter' => 'https://bacakomik.net/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//a",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'datech')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'animposx')]",
                'title' => [
                    'xpath' => ".//h4",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'typeflag')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'color' => [
                    'xpath' => ".//*[contains(@class, 'warnalabel')]",
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Jenis')]/parent::*//a",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Status')]/parent::*",
                        'regex' => '/([\s\n\t]+)?status\:?([\s\n\t]+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Pengarang')]/parent::*",
                        'regex' => '/([\s\n\t]+)?pengarang\:?([\s\n\t]+)?/i',
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genre-info')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapter_list']//*[contains(@class, 'lchx')]//a",
                    'num' => ".//chapter",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article//*[contains(@class, 'chapter-content')]",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b\.?).*/i',
                ],
                'cover' => [ //no parent
                    'xpath' => "//*[contains(@class, 'infoanime')]//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nextprev')]//a[@rel='next']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nextprev')]//a[@rel='prev']",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[@id='anjay_ini_id_kh']//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
            ],
        ];

        return $data;
    }

    public static function tukangkomik()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://tukangkomik.id',
                'latest' => 'https://tukangkomik.id/manga/?page={$page}&order=update',
                'search' => 'https://tukangkomik.id/page/{$page}/?s={$value}',
                'advanced' => 'https://tukangkomik.id/manga/?page={$page}&{$value}',
                'series' => 'https://tukangkomik.id/manga/{$slug}/',
                'chapter' => 'https://tukangkomik.id/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Judul Alternatif')]/parent::*",
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Type')]//a",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Status')]",
                        'regex' => '/([\s\n\t]+)?status\:?([\s\n\t]+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Author')]/parent::*//span",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Artist')]/parent::*//span",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'mgen')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function ikiru()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://ikiru.one',
                'latest' => 'https://ikiru.one/manga/?page={$page}&order=update',
                'search' => 'https://ikiru.one/page/{$page}/?s={$value}',
                'advanced' => 'https://ikiru.one/manga/?page={$page}&{$value}',
                'series' => 'https://ikiru.one/manga/{$slug}/',
                'chapter' => 'https://ikiru.one/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Alternative Title')]/parent::*",
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Type')]//a",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Status')]",
                        'regex' => '/([\s\n\t]+)?status\:?([\s\n\t]+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Author')]/parent::*//span",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Artist')]/parent::*//span",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'mgen')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function maid()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'koidezign',
            'lang' => 'id',
            'url' => [
                'host' => 'https://www.maid.my.id',
                'latest' => 'https://www.maid.my.id/page/{$page}/',
                'search' => 'https://www.maid.my.id/page/{$page}/?s={$value}',
                'advanced' => 'https://www.maid.my.id/advanced-search/page/{$page}/?{$value}',
                'series' => 'https://www.maid.my.id/manga/{$slug}/',
                'chapter' => 'https://www.maid.my.id/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'chapter')]//li//a",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'chapter')]//li//*[contains(@class, 'date')]",
                ],
            ],
            'search' => [
                'parent' => "//*[contains(@class, 'flexbox2')]//*[contains(@class, 'flexbox2-content')]",
                'title' => [
                    'attr' => 'title',
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'flexbox4')]//*[contains(@class, 'flexbox4-content')]",
                'title' => [
                    'xpath' => ".//*[@class='title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'series-flex')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//*[contains(@class, 'series-title')]//h2",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'series-thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'series-infoz')]//*[contains(@class, 'type')]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'series-infoz')]//*[contains(@class, 'status')]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'series-infolist')]//*[contains(text(), 'Author')]/parent::*//span",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'series-genres')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'series-synops')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'series-chapterlist')]//*[contains(@class, 'flexch-infoz')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//main//*[@id='chapnav']/parent::*",
                'title' => [
                    'xpath' => ".//*[@id='chapnav']//*[contains(@class, 'title')]//a",
                ],
                'cover' => [
                    'xpath' => ".//*[@id='chapnav']//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'rightnav')]//a[@rel='next']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'leftnav')]//a[@rel='prev']",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reader-area')]//img",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function komiksin()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komiksin.id',
                'latest' => 'https://komiksin.id/manga/?page={$page}&order=update',
                'search' => 'https://komiksin.id/page/{$page}/?s={$value}',
                'advanced' => 'https://komiksin.id/manga/?page={$page}&{$value}',
                'series' => 'https://komiksin.id/manga/{$slug}/',
                'chapter' => 'https://komiksin.id/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'color' => [
                    'xpath' => ".//*[contains(@class, 'colored')]",
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'seriestualt')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Type')]/parent::*//td[2]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Status')]/parent::*//td[2]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Author')]/parent::*//td[2]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Artist')]/parent::*//td[2]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'seriestugenre')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function kiryuu()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://kiryuu.org',
                'latest' => 'https://kiryuu.org/manga/?page={$page}&order=update',
                'search' => 'https://kiryuu.org/page/{$page}/?s={$value}',
                'advanced' => 'https://kiryuu.org/manga/?page={$page}&{$value}',
                'series' => 'https://kiryuu.org/manga/{$slug}/',
                'chapter' => 'https://kiryuu.org/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-cfsrc',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'color' => [
                    'xpath' => ".//*[contains(@class, 'colored')]",
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'seriestualt')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-cfsrc',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Type')]/parent::*//td[2]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Status')]/parent::*//td[2]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Author')]/parent::*//td[2]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Artist')]/parent::*//td[2]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'seriestugenre')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function komikcast()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'enduser',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komikcast.cz',
                'latest' => 'https://komikcast.cz/daftar-komik/page/{$page}/?order=update',
                'search' => 'https://komikcast.cz/page/{$page}/?s={$value}',
                'advanced' => 'https://komikcast.cz/daftar-komik/page/{$page}/?{$value}',
                'series' => 'https://komikcast.cz/komik/{$slug}/',
                'chapter' => 'https://komikcast.cz/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'list-update_item-info')]//*[contains(@class, 'chapter')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'list-update_items-wrapper')]//*[contains(@class, 'list-update_item-image')]/../..", //same as parent::
                'title' => [
                    'xpath' => ".//*[@class='title']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'komik_info')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'komik_info-content-native')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'komik_info-content-thumbnail')]//meta[@itemprop='url']",
                    'attr' => 'content',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'komik_info-content-info-type')]//a",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'komik_info-content-meta')]//*[contains(text(), 'Status')]/parent::*",
                        'regex' => '/([\s\n\t]+)?status\:?([\s\n\t]+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'komik_info-content-meta')]//*[contains(text(), 'Author')]/parent::*[contains(@class, 'komik_info-content-info')]",
                        'regex' => '/([\s\n\t]+)?author\:?[\s\n\t]+/i',
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'komik_info-content-genre')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'komik_info-description-sinopsis')]//*[not(*[contains(@class, 'post-views')])]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapter-wrapper']//*[contains(@class, 'komik_info-chapters-item')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'chapter_')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nextprev')]//a[@rel='next']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nextprev')]//a[@rel='prev']",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'main-reading-area')]//img[@src]",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function pojokmanga()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://pojokmanga.org/',
                'latest' => 'https://pojokmanga.org/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://pojokmanga.org/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://pojokmanga.org/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://pojokmanga.org/komik/{$slug}/',
                'chapter' => 'https://pojokmanga.org/komik/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'chapter')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'post-on')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'c-tabs-item__content')]",
                'title' => [
                    'xpath' => ".//*[@class='post-title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'wp-pagenavi')]//a[contains(@class, 'page larger')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'wp-pagenavi')]//a[contains(@class, 'page smaller')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..", //same as parent::
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..", //same as parent::
                        'regex' => '/([\s\n\t]+)?t[iy]pe\:?[\s\n\t]+/i',
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //similar to 'alternative' & 'detail > type'
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'author-content')]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'artist-content')]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'description-summary')]//*[contains(@class, 'summary__content')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'ajax' => 'ajax/chapters/',
                    'parent' => "//*[@id='manga-chapters-holder']", //ajax
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]",
                    'slug' => true,
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'next_page')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'prev_page')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }

    public static function klikmanga()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://klikmanga.id/',
                'latest' => 'https://klikmanga.id/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://klikmanga.id/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://klikmanga.id/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://klikmanga.id/manga/{$slug}/',
                'chapter' => 'https://klikmanga.id/manga/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'chapter')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'post-on')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'c-tabs-item__content')]",
                'title' => [
                    'xpath' => ".//*[@class='post-title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'ajax' => true,
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Judul Lain')]/../..", //same as parent::
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Tipe')]/../..", //same as parent::
                        'regex' => '/([\s\n\t]+)?t[iy]pe\:?[\s\n\t]+/i',
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //similar to 'alternative' & 'detail > type'
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'author-content')]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'artist-content')]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'description-summary')]//*[contains(@class, 'summary__content')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]",
                    'slug' => true,
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'next_page')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'prev_page')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }

    public static function komiknesia()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komiknesia.xyz',
                'latest' => 'https://komiknesia.xyz/manga/?page={$page}&order=update',
                'search' => 'https://komiknesia.xyz/page/{$page}/?s={$value}',
                'advanced' => 'https://komiknesia.xyz/manga/?page={$page}&{$value}',
                'series' => 'https://komiknesia.xyz/manga/{$slug}/',
                'chapter' => 'https://komiknesia.xyz/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'color' => [
                    'xpath' => ".//*[contains(@class, 'colored')]",
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'alternative')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Type')]//a"
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Status')]",
                        'regex' => '/([\s\n\t]+)?status\:?([\s\n\t]+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Author')]",
                        'regex' => '/([\s\n\t]+)?author\:?[\s\n\t]+/i',
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Artist')]",
                        'regex' => '/([\s\n\t]+)?artist\:?[\s\n\t]+/i',
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'mgen')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b\.?).*/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function komiklovers()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komiklovers.com',
                'latest' => 'https://komiklovers.com/komik/?page={$page}&order=update',
                'search' => 'https://komiklovers.com/page/{$page}/?s={$value}',
                'advanced' => 'https://komiklovers.com/komik/?page={$page}&{$value}',
                'series' => 'https://komiklovers.com/komik/{$slug}/',
                'chapter' => 'https://komiklovers.com/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'color' => [
                    'xpath' => ".//*[contains(@class, 'colored')]",
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'seriestualt')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Type')]/parent::*//td[2]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Status')]/parent::*//td[2]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Author')]/parent::*//td[2]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Artist')]/parent::*//td[2]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'seriestugenre')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function komiku()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'none',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komiku.id',
                'latest' => 'https://api.komiku.id/manga/page/{$page}/?orderby=modified',
                'search' => 'https://api.komiku.id/manga/page/{$page}/?s={$value}',
                'advanced' => 'https://api.komiku.id/manga/page/{$page}/?{$value}',
                'series' => 'https://komiku.id/manga/{$slug}/',
                'chapter' => 'https://komiku.id/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(text(), 'Terbaru:')]/parent::*//span[2]",
                    'regex' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
            ],
            'LS' => [
                'parent' => "//body//*[contains(@class, 'bge') and not(contains(@class, 'bgei'))]",
                'title' => [
                    'xpath' => ".//h3",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'tpe1_inf')]//b",
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'ajax' => true,
                ],
            ],
            'series' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'j2')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'ims')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'inftable')]//*[contains(text(), 'Jenis')]/parent::*//td[2]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'inftable')]//*[contains(text(), 'Status')]/parent::*//td[2]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'inftable')]//*[contains(text(), 'Pengarang')]/parent::*//td[2]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genre')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'desc')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='daftarChapter']//a",
                    'num' => ".//span",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[@id='main']",
                'title' => [
                    'xpath' => ".//header//a",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b\.?).*/i',
                ],
                'nav' => [ //no parent
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'nxpr')]//svg[@data-icon='caret-right']/parent::*",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'nxpr')]//svg[@data-icon='caret-left']/parent::*",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[@id='Baca_Komik']//img",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function mgkomik()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://mgkomik.id/',
                'latest' => 'https://mgkomik.id/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://mgkomik.id/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://mgkomik.id/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://mgkomik.id/komik/{$slug}/',
                'chapter' => 'https://mgkomik.id/komik/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'chapter')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'post-on')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'c-tabs-item__content')]",
                'title' => [
                    'xpath' => ".//*[@class='post-title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//a[contains(@href, 'genres/manga') or contains(@href, 'genres/manhwa') or contains(@href, 'genres/manhua')]",
                    'attr' => 'href',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'wp-pagenavi')]//a[contains(@class, 'page larger')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'wp-pagenavi')]//a[contains(@class, 'page smaller')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..", //same as parent::
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..", //same as parent::
                        'regex' => '/([\s\n\t]+)?t[iy]pe\:?[\s\n\t]+/i',
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //similar to 'alternative' & 'detail > type'
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'description-summary')]//*[contains(@class, 'summary__content')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]",
                    'slug' => true,
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'next_page')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'prev_page')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function shinigami()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://shinigami05.com/',
                'latest' => 'https://shinigami05.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://shinigami05.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://shinigami05.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://shinigami05.com/series/{$slug}/',
                'chapter' => 'https://shinigami05.com/series/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'chapter')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'post-on')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'c-tabs-item__content')]",
                'title' => [
                    'xpath' => ".//*[@class='post-title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/series/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'ajax' => true,
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indonesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..", //same as parent::
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..", //same as parent::
                        'regex' => '/([\s\n\t]+)?t[iy]pe\:?[\s\n\t]+/i',
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //similar to 'alternative' & 'detail > type'
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'author-content')]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'artist-content')]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'description-summary')]//*[contains(@class, 'summary__content')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'ajax' => 'ajax/chapters/',
                    'parent' => "//*[@id='manga-chapters-holder']", //ajax
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'num' => ".//*[contains(@class, 'chapter-manhwa-title')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indonesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'content-area')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]",
                    'slug' => true,
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'next_page')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'prev_page')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }

    public static function leviatanscans()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'en',
            'url' => [
                'host' => 'https://lscomic.com/',
                'latest' => 'https://lscomic.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://lscomic.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://lscomic.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://lscomic.com/manga/{$slug}/',
                'chapter' => 'https://lscomic.com/manga/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'chapter')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'post-on')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'c-tabs-item__content')]",
                'title' => [
                    'xpath' => ".//*[@class='post-title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'paging-navigation')]//*[contains(@class, 'nav-previous')]//a", //reverse order
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'paging-navigation')]//*[contains(@class, 'nav-next')]//a", //reverse order
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a[contains(text(), 'manga') or contains(text(), 'manhwa') or contains(text(), 'manhua')]",
                        'regex' => '/([\s\n\t]+)?t[iy]pe\:?[\s\n\t]+/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'manga-authors')]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'manga-about')]//p",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'ajax' => 'ajax/chapters/',
                    'parent' => "//*[@id='manga-chapters-holder']", //ajax
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]",
                    'slug' => true,
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'next_page')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'prev_page')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }

    public static function reaper_scans()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'en',
            'url' => [
                'host' => 'https://reaper-scans.com',
                'latest' => 'https://reaper-scans.com/series/?page={$page}&order=update',
                'search' => 'https://reaper-scans.com/page/{$page}/?s={$value}',
                'advanced' => 'https://reaper-scans.com/series/?page={$page}&{$value}',
                'series' => 'https://reaper-scans.com/series/{$slug}/',
                'chapter' => 'https://reaper-scans.com/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'next page-numbers')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'pagination')]//a[contains(@class, 'prev page-numbers')]",
                        'attr' => 'href',
                    ],
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//a/parent::*",
                'title' => [
                    'xpath' => ".//*[@class='tt']",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'type' => [
                    'xpath' => ".//*[contains(@class, 'type')]",
                    'attr' => 'class',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'color' => [
                    'xpath' => ".//*[contains(@class, 'colored')]",
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'status')]",
                    'attr' => 'class',
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/series/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='r']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'hpage')]//a[@class='l']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'seriestualt')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Type')]/parent::*//td[2]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'status-value')]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Author')]/parent::*//td[2]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Artist')]/parent::*//td[2]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'seriestugenre')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'num' => ".//*[contains(@class, 'chapternum')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from ts_reader
            ],
        ];

        return $data;
    }

    public static function manhuaus()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'en',
            'url' => [
                'host' => 'https://manhuaus.com/',
                'latest' => 'https://manhuaus.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://manhuaus.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://manhuaus.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://manhuaus.com/manga/{$slug}/',
                'chapter' => 'https://manhuaus.com/manga/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'chapter')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'tab-meta')]//*[contains(@class, 'post-on')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'c-tabs-item__content')]",
                'title' => [
                    'xpath' => ".//*[@class='post-title']//a",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s(completed?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?)\/([^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [
                    'ajax' => true,
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..", //same as parent::
                    'regex' => '/([\s\n\t]+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?[\s\n\t]+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //similar to 'alternative' & 'detail > type'
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'author-content')]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(@class, 'artist-content')]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'description-summary')]//*[contains(@class, 'summary__content')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'ajax' => 'ajax/chapters/',
                    'parent' => "//*[contains(@class, 'page-content-listing')]", //ajax
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'content-area')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]",
                    'slug' => true,
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b)',
                    'regex2' => '\-.*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'next_page')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[contains(@class, 'nav-links')]//a[contains(@class, 'prev_page')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[contains(@class, 'reading-content')]//*[@id='wp-manga-current-chap']//..//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }
}
