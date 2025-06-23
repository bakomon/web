<?php

// XPath

namespace Api\Services;

class xSelector
{
    public static $source_default = 'ikiru';

    public static $source_lists = ['bacakomik', 'tukangkomik', 'ikiru', 'maid', 'komiksin', 'kiryuu', 'komikcast', 'pojokmanga', 'klikmanga', 'lumoskomik', 'komiklovers', 'cosmicscans', 'manhwalist', 'ainzscans', 'soulscans', 'westmanga', 'komikstation', 'softkomik', 'webtoons', 'komiku', 'mgkomik', 'shinigami', 'leviatanscans', 'reaper_scans', 'manhuaus', 'mangapark', 'mangasee', 'comick'];

    public static function bacakomik()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'eastheme',
            'lang' => 'id',
            'url' => [
                'host' => 'https://bacakomik.one',
                'latest' => 'https://bacakomik.one/komik-terbaru/page/{$page}/',
                'search' => 'https://bacakomik.one/page/{$page}/?s={$value}',
                'advanced' => 'https://bacakomik.one/daftar-komik/page/{$page}/?{$value}',
                'series' => 'https://bacakomik.one/komik/{$slug}/',
                'chapter' => 'https://bacakomik.one/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
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
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Rilis')]/parent::*",
                        'regex' => '/(\s+)?rilis\:?(\s+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Pengarang')]/parent::*",
                        'regex' => '/(\s+)?pengarang\:?(\s+)?/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article//*[contains(@class, 'chapter-content')]",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:main[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'cover' => [ //no parent
                    'xpath' => "//*[contains(@class, 'infoanime')]//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                'host' => 'https://tukangkomik.co',
                'latest' => 'https://tukangkomik.co/manga/?page={$page}&order=update',
                'search' => 'https://tukangkomik.co/page/{$page}/?s={$value}',
                'advanced' => 'https://tukangkomik.co/manga/?page={$page}&{$value}',
                'series' => 'https://tukangkomik.co/manga/{$slug}/',
                'chapter' => 'https://tukangkomik.co/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Judul Alternatif')]/parent::*",
                    'regex' => '/(\s+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?\s+/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Released')]/parent::*//span",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
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
                'host' => 'https://id.ikiru.wtf',
                'latest' => 'https://id.ikiru.wtf/manga/?page={$page}&order=update',
                'search' => 'https://id.ikiru.wtf/page/{$page}/?s={$value}',
                'advanced' => 'https://id.ikiru.wtf/manga/?page={$page}&{$value}',
                'series' => 'https://id.ikiru.wtf/manga/{$slug}/',
                'chapter' => 'https://id.ikiru.wtf/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Alternative Title')]/parent::*",
                    'regex' => '/(\s+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?\s+/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Released')]/parent::*//span",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
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
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Released')]/parent::*//td[2]",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
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
                'host' => 'https://kiryuu01.com',
                'latest' => 'https://kiryuu01.com/manga/?page={$page}&order=update',
                'search' => 'https://kiryuu01.com/page/{$page}/?s={$value}',
                'advanced' => 'https://kiryuu01.com/manga/?page={$page}&{$value}',
                'series' => 'https://kiryuu01.com/manga/{$slug}/',
                'chapter' => 'https://kiryuu01.com/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Released')]/parent::*//td[2]",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
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
                'host' => 'https://komikcast02.com',
                'latest' => 'https://komikcast02.com/daftar-komik/page/{$page}/?order=update',
                'search' => 'https://komikcast02.com/page/{$page}/?s={$value}',
                'advanced' => 'https://komikcast02.com/daftar-komik/page/{$page}/?{$value}',
                'series' => 'https://komikcast02.com/komik/{$slug}/',
                'chapter' => 'https://komikcast02.com/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'komik_info-content-meta')]//*[contains(text(), 'Released')]/parent::*",
                        'regex' => '/(\s+)?(released?|terbitan)\:?(\s+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'komik_info-content-meta')]//*[contains(text(), 'Author')]/parent::*[contains(@class, 'komik_info-content-info')]",
                        'regex' => '/(\s+)?author\:?\s+/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'chapter_')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                'host' => 'https://pojokmanga.info',
                'latest' => 'https://pojokmanga.info/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://pojokmanga.info/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://pojokmanga.info/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://pojokmanga.info/komik/{$slug}/',
                'chapter' => 'https://pojokmanga.info/komik/{$slug}/chapter-{$chapter}/?style=list',
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
                    'xpath' => ".//*[contains(@class, 'mg_genres')]//a[contains(@href, '/manga') or contains(@href, '/manhwa') or contains(@href, '/manhua')]",
                    'attr' => 'href',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Release')]/../..//a", //same as parent::
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
                    'fetch' => 'ajax/chapters/',
                    'area' => ".//*[@id='manga-chapters-holder']", //fetch
                    'xpath' => ".//*[@id='manga-chapters-holder']//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/komik/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
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
                'host' => 'https://klikmanga.com',
                'latest' => 'https://klikmanga.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://klikmanga.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://klikmanga.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://klikmanga.com/manga/{$slug}/',
                'chapter' => 'https://klikmanga.com/manga/{$slug}/chapter-{$chapter}/?style=list',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'manual' => true,
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Judul Lain')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Tipe')]/../..", //same as parent::
                        'regex' => '/(\s+)?t[iy]pe\:?\s+/i',
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //similar 'detail > type'
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Terbitan')]/../..//a", //same as parent::
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/manga/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }

    public static function lumoskomik()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://lumos01.com',
                'latest' => 'https://lumos01.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://lumos01.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://lumos01.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://lumos01.com/komik/{$slug}/',
                'chapter' => 'https://lumos01.com/komik/{$slug}/chapter-{$chapter}/?style=list',
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
                    'xpath' => ".//*[contains(@class, 'mg_genres')]//a[contains(@href, '/manga') or contains(@href, '/manhwa') or contains(@href, '/manhua')]",
                    'attr' => 'href',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Release')]/../..//a", //same as parent::
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
                    'xpath' => ".//*[@id='tab-manga-summary']//*[contains(@class, 'post-content_item')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/komik/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                ],
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function cosmicscans()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://cosmic345.co',
                'latest' => 'https://cosmic345.co/manga/?page={$page}&order=update',
                'search' => 'https://cosmic345.co/page/{$page}/?s={$value}',
                'advanced' => 'https://cosmic345.co/manga/?page={$page}&{$value}',
                'series' => 'https://cosmic345.co/manga/{$slug}/',
                'chapter' => 'https://cosmic345.co/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Alternative Title')]/parent::*",
                    'regex' => '/(\s+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?\s+/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Released')]/parent::*//span",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function manhwalist()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://manhwalist.xyz',
                'latest' => 'https://manhwalist.xyz/manga/?page={$page}&order=update',
                'search' => 'https://manhwalist.xyz/page/{$page}/?s={$value}',
                'advanced' => 'https://manhwalist.xyz/manga/?page={$page}&{$value}',
                'series' => 'https://manhwalist.xyz/manga/{$slug}/',
                'chapter' => 'https://manhwalist.xyz/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Released')]",
                        'regex' => '/(\s+)?(released?|terbitan)\:?(\s+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Author')]",
                        'regex' => '/(\s+)?author\:?\s+/i',
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Artist')]",
                        'regex' => '/(\s+)?artist\:?\s+/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                    'regex' => '/(?:(?:main[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function ainzscans()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://ainzscans.net',
                'latest' => 'https://ainzscans.net/series/?page={$page}&order=update',
                'search' => 'https://ainzscans.net/page/{$page}/?s={$value}',
                'advanced' => 'https://ainzscans.net/series/?page={$page}&{$value}',
                'series' => 'https://ainzscans.net/series/{$slug}/',
                'chapter' => 'https://ainzscans.net/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/series/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infotable')]//*[contains(text(), 'Released')]/parent::*//td[2]",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function soulscans()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://soulscans.my.id',
                'latest' => 'https://soulscans.my.id/latest-update/page/{$page}/',
                'search' => 'https://soulscans.my.id/page/{$page}/?s={$value}',
                'advanced' => 'https://soulscans.my.id/manga/?page={$page}&{$value}',
                'series' => 'https://soulscans.my.id/manga/{$slug}/',
                'chapter' => 'https://soulscans.my.id/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxdate')]",
                ],
            ],
            'advanced' => [
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Released')]",
                        'regex' => '/(\s+)?(released?|terbitan)\:?(\s+)?/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Author')]",
                        'regex' => '/(\s+)?author\:?\s+/i',
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Artist')]",
                        'regex' => '/(\s+)?artist\:?\s+/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function westmanga()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://westmanga.fun',
                'latest' => 'https://westmanga.fun/manga/?page={$page}&order=update',
                'search' => 'https://westmanga.fun/page/{$page}/?s={$value}',
                'advanced' => 'https://westmanga.fun/manga/?page={$page}&{$value}',
                'series' => 'https://westmanga.fun/manga/{$slug}/',
                'chapter' => 'https://westmanga.fun/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function komikstation()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komikstation.co',
                'latest' => 'https://komikstation.co/manga/?page={$page}&order=update',
                'search' => 'https://komikstation.co/page/{$page}/?s={$value}',
                'advanced' => 'https://komikstation.co/manga/?page={$page}&{$value}',
                'series' => 'https://komikstation.co/manga/{$slug}/',
                'chapter' => 'https://komikstation.co/{$slug}-chapter-{$chapter}/',
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
            'advanced' => [ //if "nav" is in "search" and "advanced nav" is same as "LS"
                'nav' => 'LS',
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'listupd')]//*[contains(@class, 'bsx')]",
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Judul Alternatif')]/parent::*",
                    'regex' => '/(\s+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?\s+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Tipe')]//a",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'tsinfo')]//*[contains(text(), 'Status')]",
                        'regex' => '/(\s+)?status\:?(\s+)?/i',
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Terbitan')]/parent::*//span",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Pengarang')]/parent::*//span",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Ilustrator')]/parent::*//span",
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'name' => 'nextUrl',
                    ],
                    'prev' => [
                        'name' => 'prevUrl',
                    ],
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function softkomik()
    {
        $data = [
            'lang' => 'id',
            'parser' => true,
            'url' => [
                'host' => 'https://softkomik.com',
                'latest' => 'https://softkomik.com/komik/library?page={$page}&sortBy=new',
                'search' => 'https://softkomik.com/komik/list?page={$page}&name={$value}',
                'advanced' => 'https://softkomik.com/komik/library?page={$page}&{$value}',
            ],
        ];

        return $data;
    }

    public static function webtoons()
    {
        $data = [
            'lang' => 'id',
            'parser' => true,
            'scid' => [ 'series' ],
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
                    'regex' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'manual' => true,
                ],
            ],
            'series' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[@id='main']",
                'title' => [
                    'xpath' => ".//header//a",
                    'regex' => '/(?:(?:main[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'nav' => [ //no parent
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                'host' => 'https://mgkomik.org',
                'latest' => 'https://mgkomik.org/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://mgkomik.org/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://mgkomik.org/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://mgkomik.org/komik/{$slug}/',
                'chapter' => 'https://mgkomik.org/komik/{$slug}/chapter-{$chapter}/?style=list',
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
                    'xpath' => ".//*[contains(@class, 'mg_genres')]//a[contains(@href, '/manga') or contains(@href, '/manhwa') or contains(@href, '/manhua')]",
                    'attr' => 'href',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/komik/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Release')]/../..//a", //same as parent::
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
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
                'host' => 'https://shinigami09.com',
                'latest' => 'https://shinigami09.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://shinigami09.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://shinigami09.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://shinigami09.com/series/{$slug}/',
                'chapter' => 'https://shinigami09.com/series/{$slug}/chapter-{$chapter}/?style=list',
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
                'type' => [
                    'xpath' => ".//*[contains(@class, 'mg_genres')]//a[contains(@href, '/manga') or contains(@href, '/manhwa') or contains(@href, '/manhua')]",
                    'attr' => 'href',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'mg_status')]",
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/series/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'manual' => true,
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Type')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Release')]/../..//a", //same as parent::
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
                'chapter' => [ //no parent
                    'xpath' => "//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'num' => ".//*[contains(@class, 'chapter-manhwa-title')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'content-area')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/series/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
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
                'host' => 'https://lscomic.com',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'genres-content')]//a[contains(@href, '/manga') or contains(@href, '/manhwa') or contains(@href, '/manhua')]",
                        'regex' => '/(\s+)?t[iy]pe\:?\s+/i',
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
                    'fetch' => 'ajax/chapters/',
                    'area' => ".//*[@id='manga-chapters-holder']", //fetch
                    'xpath' => ".//*[@id='manga-chapters-holder']//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/manga/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
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
            'theme' => 'madara',
            'lang' => 'en',
            'url' => [
                'host' => 'https://reaper-scans.com',
                'latest' => 'https://reaper-scans.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://reaper-scans.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://reaper-scans.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://reaper-scans.com/manga/{$slug}/',
                'chapter' => 'https://reaper-scans.com/manga/{$slug}/chapter-{$chapter}/?style=list',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'ch-num')]",
                ],
                'date' => [
                    'xpath' => ".//*[contains(@class, 'ch-date')]",
                ],
            ],
            'LS' => [
                'parent' => "//*[contains(@class, 'original')]//*[contains(@class, 'unit')]",
                'title' => [
                    'xpath' => ".//*[@class='info']//a",
                ],
                'cover' => [
                    'xpath' => ".//img[not(contains(@class, 'flag-icon'))]",
                    'attr' => 'src',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [ //no parent
                    'regex' => '/.*page[\/=](\d+)[\/&]?/i',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'page-item') and not(contains(@class, 'disabled'))]//a[@rel='next']",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'page-item') and not(contains(@class, 'disabled'))]//a[@rel='prev']",
                        'attr' => 'href',
                    ],
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'manga-detail')]//*[contains(@class, 'container')]",
                'shortlink' => [ //no parent
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'alternative-title')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'main-cover')]//img[not(contains(@class, 'flag-icon'))]",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(@class, 'manga-stats')]//*[contains(text(), 'Type')]/parent::*//span[2]",
                    ],
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'manga-stats')]//*[contains(text(), 'Status')]/parent::*//span[2]",
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(@class, 'manga-stats')]//*[contains(text(), 'Release')]/parent::*//span[2]",
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'manga-stats')]//*[contains(text(), 'Author')]/parent::*//span[2]",
                    ],
                    'artist' => [
                        'xpath' => ".//*[contains(@class, 'manga-stats')]//*[contains(text(), 'Artist')]/parent::*//span[2]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[contains(@class, 'genre-list')]//*[contains(@class, 'genre-link')]",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'description')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'm-list')]//*[contains(@class, 'list-body-hh')]//a",
                    'num' => ".//*[contains(text(), 'Chapter')]/parent::*",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'reading-content')]",
                'title' => [
                    'xpath' => ".//a[contains(@class, 'manga-title')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[@id='number-go-left']",
                        'attr' => 'onclick',
                    ],
                    'prev' => [
                        'xpath' => ".//*[@id='number-go-right']",
                        'attr' => 'onclick',
                    ],
                ],
                'images' => [
                    'xpath' => ".//*[@id='ch-images']//*[contains(@class, 'protected-image-data')]",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
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
                'host' => 'https://manhuaus.com',
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
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)?\/([^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [
                    'manual' => true,
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'post-content')]//*[contains(text(), 'Alternative')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'summary_image')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
                'detail' => [
                    'status' => [
                        'xpath' => ".//*[contains(@class, 'post-status')]//*[contains(text(), 'Status')]/../..//*[contains(@class, 'summary-content')]", //same as parent::
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'content-area')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/manga/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[contains(@class, 'read-container')]//*[contains(@class, 'reading-content')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-src',
                ],
            ],
        ];

        return $data;
    }

    public static function mangapark()
    {
        $data = [
            'cms' => 'none',
            'theme' => 'none',
            'lang' => 'en',
            'scid' => [ 'series', 'chapter' ],
            'headers' => [
                'Cookie' => 'HMACCOUNT=1FD37FB736BC968F; bset=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiaWQiOiI2NzE1OThjNTg0NjhjNWVlZGZkYzY3ZmMiLCJpYXQiOjE3Mjk1NjQ3MzR9.PDyxH5ZSktjQBUuNevwosL1VM8Vsw8zi8Ao-azeu20w',
            ],
            'url' => [
                'host' => 'https://mangapark.net',
                'latest' => 'https://mangapark.net/search?page={$page}&sortby=field_update',
                'search' => 'https://mangapark.net/search?page={$page}&sortby=field_update&word={$value}',
                'advanced' => 'https://mangapark.net/search?page={$page}&{$value}',
                'series' => 'https://mangapark.net/title/{$slug}',
                'chapter' => 'https://mangapark.net/title/{$slug}/{$chapter}',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//a[contains(@href, '-chapter-') or contains(@href, '-ch-')]",
                    'regex' => '/vol\.\d+\s/i',
                ],
                'date' => [
                    'xpath' => ".//time[@data-time]",
                    'attr' => 'data-time',
                ],
            ],
            'LS' => [
                'parent' => "//main//*[@*[name()='q:key' and .='q4_9']]",
                'title' => [
                    'xpath' => ".//h3",
                ],
                'cover' => [
                    'xpath' => ".//img",
                    'attr' => 'src',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/title/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?|title)\/([^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [
                    'manual' => true,
                ],
            ],
            'series' => [
                'parent' => "//main",
                'title' => [
                    'xpath' => ".//h3",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?|title)\s/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[@*[name()='q:key' and .='tz_2']]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'items-start')]//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='30_2']]//*[@*[name()='q:key' and (.='manga' or .='manhwa' or .='manhua')]]//*[@*[name()='q:key' and .='kd_0']]",
                    ],
                    'status' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='Yn_8']]//*[@*[name()='q:key' and .='Yn_5']]",
                    ],
                    'author' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='tz_4']]",
                    ],
                    'genre' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='30_2']]//*[@*[name()='q:key' and .='kd_0']]",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(@class, 'limit-html-p')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@data-name='chapter-list']//a[contains(@href, '-chapter-') or contains(@href, '-ch-') or contains(@href, '-episode-') or contains(@href, '-ep-')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//main",
                'title' => [
                    'xpath' => ".//h3//a",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='0B_7']]//a[contains(@href, '-chapter-') or contains(@href, '-ch-')]//*[@name='angle-right']/parent::*",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='0B_7']]//a[contains(@href, '-chapter-') or contains(@href, '-ch-')]//*[@name='angle-left']/parent::*",
                        'attr' => 'href',
                    ],
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), '\"objs\":[[')]",
                ],
                'images' => [], //images from <script>
            ],
        ];

        return $data;
    }

    public static function mangasee()
    {
        $data = [
            'cms' => 'none',
            'theme' => 'none',
            'lang' => 'en',
            'scid' => [ 'series', 'chapter' ],
            'url' => [
                'host' => 'https://weebcentral.com',
                'latest' => 'https://weebcentral.com/latest-updates/{$page}',
                'search' => 'https://weebcentral.com/search/data?limit=32&order=Descending&display_mode=Full+Display&adult=False&excluded_tag=Gender+Bender&excluded_tag=Hentai&excluded_tag=Shoujo+Ai&excluded_tag=Shounen+Ai&excluded_tag=Yaoi&excluded_tag=Yuri&offset={$page}&text={$value}',
                'advanced' => 'https://weebcentral.com/search/data?limit=32&order=Descending&display_mode=Full+Display&adult=False&excluded_tag=Gender+Bender&excluded_tag=Hentai&excluded_tag=Shoujo+Ai&excluded_tag=Shounen+Ai&excluded_tag=Yaoi&excluded_tag=Yuri&offset={$page}&{$value}',
                'series' => 'https://weebcentral.com/series/{$slug}',
                'chapter' => 'https://weebcentral.com/chapters/{$chapter}',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//a[contains(@href, '/chapters/')]//*[contains(text(), 'Chapter')]",
                ],
                'date' => [
                    'xpath' => ".//a[contains(@href, '/chapters/')]//*[contains(@class, 'text-datetime')]",
                ],
            ],
            'search' => [ //parent is same as "LS" parent
                'title' => [
                    'xpath' => ".//section[contains(@class, 'hidden lg:block')]//a",
                ],
                'type' => [
                    'xpath' => ".//section[contains(@class, 'hidden lg:block')]//*[contains(text(), 'Type')]/parent::*//span",
                    'content' => true,
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//section[contains(@class, 'hidden lg:block')]//*[contains(text(), 'Status')]/parent::*//span",
                    'content' => true,
                    'regex' => '/\s?(end|completed?|finish(ed)?|tamat)/i',
                ],
            ],
            'LS' => [
                'parent' => "//article[contains(@class, 'bg-base-')]",
                'title' => [
                    'xpath' => ".//a[contains(@href, '/chapters/')]//*[contains(@class, 'truncate')]",
                ],
                'cover' => [
                    'xpath' => ".//picture//img",
                    'attr' => 'src',
                ],
                'link' => [
                    'xpath' => ".//a[contains(@href, '/series/')]",
                    'attr' => 'href',
                ],
                'slug' => [
                    'regex' => '/.*(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series?|title)\/([^\/]+\/[^\/]+)/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
                'nav' => [
                    'manual' => true,
                ],
            ],
            'series' => [
                'parent' => "//main//*[@id='top']",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?|title)\s/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(text(), 'Associated Name')]/parent::*//ul",
                ],
                'cover' => [
                    'xpath' => ".//picture//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//a[contains(@href, '/rss')]/ancestor::ul//*[contains(text(), 'Type')]/parent::*//a",
                    ],
                    'status' => [
                        'xpath' => ".//a[contains(@href, '/rss')]/ancestor::ul//*[contains(text(), 'Status')]/parent::*//a",
                    ],
                    'released' => [
                        'xpath' => ".//a[contains(@href, '/rss')]/ancestor::ul//*[contains(text(), 'Released')]/parent::*//span",
                    ],
                    'author' => [
                        'xpath' => ".//a[contains(@href, '/rss')]/ancestor::ul//*[contains(text(), 'Author')]/parent::*//a",
                        'regex' => '/(\s+)?author\:?\s+/i',
                    ],
                    'genre' => [
                        'xpath' => ".//a[contains(@href, '/rss')]/ancestor::ul//*[contains(text(), 'Tag')]/parent::*//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[contains(text(), 'Description')]/parent::*//p",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'fetch' => '/full-chapter-list',
                    'area' => ".//*[@id='chapter-list']", //fetch
                    'xpath' => ".//*[@id='chapter-list']//*[contains(@x-data, 'new_chapter')]//a",
                    'num' => ".//*[contains(text(), 'Chapter') or contains(text(), 'Episode')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//main",
                'title' => [
                    'xpath' => ".//a[contains(@href, '/series/')]//*[contains(@class, 'truncate')]",
                ],
                'nav' => [
                    'fetch' => 'https://weebcentral.com/series/{$slug}/chapter-select?current_chapter={$chapter}',
                    'area' => ".//*[@id='chapter-select-body']", //fetch
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?:(?!\bep\b).)*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?si?(?:on)?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[@id='selected_chapter']/preceding::a[contains(@href, '/chapters/')][1]",
                        'attr' => 'href',
                        'num' => true,
                    ],
                    'prev' => [
                        'xpath' => ".//*[@id='selected_chapter']/following-sibling::a[contains(@href, '/chapters/')]",
                        'attr' => 'href',
                        'num' => true,
                    ],
                ],
                'images' => [
                    'fetch' => '/images?reading_style=long_strip',
                    'area' => ".//*[contains(@hx-get, '/images')]", //fetch
                    'xpath' => ".//*[contains(@hx-get, '/images')]//img[contains(@alt, 'Page')]",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function comick()
    {
        $data = [
            'lang' => 'en',
            'parser' => true,
            'scid' => [ 'chapter' ],
            'url' => [
                'host' => 'https://comick.io',
                'latest' => 'https://comick.io/search?excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri&type=comic&showall=true&page={$page}&limit=49&sort=uploaded',
                'search' => 'https://comick.io/search?excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri&type=comic&showall=true&page={$page}&limit=49&q={$value}',
                'advanced' => 'https://comick.io/search?excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri&type=comic&showall=true&page={$page}&limit=49&{$value}',
            ],
        ];

        return $data;
    }
}
