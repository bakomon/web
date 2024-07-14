<?php

// XPath

namespace Api\Services;

class xSelector
{
    public static $source_default = 'mangatale';

    public static $source_lists = ['bacakomik', 'tukangkomik', 'mangatale', 'maid', 'komikindo', 'mgkomik', 'shinigami', 'kiryuu', 'komikcast', 'pojokmanga', 'klikmanga', 'leviatanscans', 'reaperscans'];

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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                        'regex' => '/([\s\n\t]+)?status\:?[\s\n\t]+/i',
                    ],
                    'author' => [
                        'xpath' => ".//*[contains(@class, 'infox')]//*[contains(text(), 'Pengarang')]/parent::*",
                        'regex' => '/([\s\n\t]+)?pengarang\:?[\s\n\t]+/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article//*[contains(@class, 'chapter-content')]",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'cover' => [ //no parent
                    'xpath' => "//*[@id='content']//*[contains(@class, 'infoanime')]//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
                'nav' => [
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                'nav' => [
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                        'regex' => '/([\s\n\t]+)?status\:?[\s\n\t]+/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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

    public static function mangatale()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://mangatale.co',
                'latest' => 'https://mangatale.co/manga/?page={$page}&order=update',
                'search' => 'https://mangatale.co/page/{$page}/?s={$value}',
                'advanced' => 'https://mangatale.co/manga/?page={$page}&{$value}',
                'series' => 'https://mangatale.co/manga/{$slug}/',
                'chapter' => 'https://mangatale.co/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                        'regex' => '/([\s\n\t]+)?status\:?[\s\n\t]+/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//*[contains(@class, 'series-title')]//h2",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//main//*[@id='chapnav']/parent::*",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'title-chapter')]",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'cover' => [
                    'xpath' => ".//*[@id='chapnav']//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                ],
                'nav' => [
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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

    public static function komikindo()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komikindo.co',
                'latest' => 'https://komikindo.co/manga/?page={$page}&order=update',
                'search' => 'https://komikindo.co/page/{$page}/?s={$value}',
                'advanced' => 'https://komikindo.co/manga/?page={$page}&{$value}',
                'series' => 'https://komikindo.co/manga/{$slug}/',
                'chapter' => 'https://komikindo.co/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                'host' => 'https://shinigami.moe/',
                'latest' => 'https://shinigami.moe/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://shinigami.moe/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://shinigami.moe/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://shinigami.moe/series/{$slug}/',
                'chapter' => 'https://shinigami.moe/series/{$slug}/chapter-{$chapter}/?style=list',
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
                    'xpath' => ".//a[contains(@href, 'genres/manga') or contains(@href, 'genres/manhwa') or contains(@href, 'genres/manhua')]",
                    'attr' => 'href',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'ajax' => true,
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'content-area')]",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'nav' => [
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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

    public static function kiryuu()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://kiryuu.id',
                'latest' => 'https://kiryuu.id/manga/?page={$page}&order=update',
                'search' => 'https://kiryuu.id/page/{$page}/?s={$value}',
                'advanced' => 'https://kiryuu.id/manga/?page={$page}&{$value}',
                'series' => 'https://kiryuu.id/manga/{$slug}/',
                'chapter' => 'https://kiryuu.id/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                'host' => 'https://komikcast.lol',
                'latest' => 'https://komikcast.lol/daftar-komik/page/{$page}/?order=update',
                'search' => 'https://komikcast.lol/page/{$page}/?s={$value}',
                'advanced' => 'https://komikcast.lol/daftar-komik/page/{$page}/?{$value}',
                'series' => 'https://komikcast.lol/komik/{$slug}/',
                'chapter' => 'https://komikcast.lol/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                        'regex' => '/([\s\n\t]+)?status\:?[\s\n\t]+/i',
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
                    'xpath' => ".//*[contains(@class, 'komik_info-description-sinopsis')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapter-wrapper']//*[contains(@class, 'komik_info-chapters-item')]//a",
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'chapter_')]",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'nav' => [
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                'host' => 'https://pojokmanga.id/',
                'latest' => 'https://pojokmanga.id/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://pojokmanga.id/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://pojokmanga.id/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://pojokmanga.id/komik/{$slug}/',
                'chapter' => 'https://pojokmanga.id/komik/{$slug}/chapter-{$chapter}/?style=list',
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                ],
                'nav' => [
                    'ajax' => true,
                ],
            ],
            'series' => [
                'parent' => "//*[contains(@class, 'site-content')]//*[contains(@class, 'type-wp-manga')]",
                'shortlink' => [
                    'xpath' => "//link[@rel='shortlink']",
                    'attr' => 'href',
                    'regex' => '/(?:\?p=|wp\.me\/)(.*)/i',
                ],
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series?)\s/i',
                    'regex2' => '/(\sbahasa?)?\sindo(nesiaa?)?/i',
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?indo(nesiaa?)?\/?|\/([\?#].*)?$)/i',
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
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
                'shortlink' => [
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
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
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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

    public static function reaperscans()
    {
        $data = [
            'cms' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'en',
            'url' => [
                'host' => 'https://reaper-scans.com',
                'latest' => 'https://reaper-scans.com/manga/?page={$page}&order=update',
                'search' => 'https://reaper-scans.com/page/{$page}/?s={$value}',
                'advanced' => 'https://reaper-scans.com/manga/?page={$page}&{$value}',
                'series' => 'https://reaper-scans.com/manga/{$slug}/',
                'chapter' => 'https://reaper-scans.com/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
                ],
            ],
            'search' => [
                'nav' => [
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
                'shortlink' => [
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
                    'regex' => '/(?:-ch(?:[ap][ap]t?er?)?)?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series)\s)?(.*)\s\d+/i',
                    'regex2' => '/(\s-)?\sch(?:apt?er|\.)?/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/]ch?(?:[ap][ap]t?(?:er)?)?',
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
}
