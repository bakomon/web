<?php

// XPath

namespace Api\Services;

class xSelector
{
    public static $source_default = 'ikiru';

    public static $source_lists = ['bacakomik', 'ikiru', 'maid', 'komiksin', 'kiryuu', 'komikcast', 'klikmanga', 'lumoskomik', 'cosmicscans', 'manhwalist', 'ainzscans', 'soulscans', 'westmanga', 'komikstation', 'mangakita', 'softkomik', 'komiku', 'mgkomik', 'shinigami', 'webtoons', 'leviatanscans', 'reaper_scans', 'manhuaus', 'mangapark', 'weebcentral', 'comick'];

    public static function bacakomik()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'eastheme',
            'lang' => 'id',
            'url' => [
                'host' => 'https://bacakomik.my',
                'latest' => 'https://bacakomik.my/komik-terbaru/page/{$page}/',
                'search' => 'https://bacakomik.my/page/{$page}/?s={$value}',
                'advanced' => 'https://bacakomik.my/daftar-komik/page/{$page}/?{$value}',
                'series' => 'https://bacakomik.my/komik/{$slug}/',
                'chapter' => 'https://bacakomik.my/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//chapter",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article//*[contains(@class, 'chapter-content')]",
                'title' => [
                    'xpath' => ".//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'cover' => [ //no parent
                    'xpath' => "//*[contains(@class, 'infoanime')]//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
                    'xpath' => ".//*[@id='anjay_ini_id_kh']/img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lazy-src',
                ],
            ],
        ];

        return $data;
    }

    public static function ikiru()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'tukutema',
            'lang' => 'id',
            'parser' => [
                'xpath' => ".//*[@id='search-results']",
            ],
            'scid' => [
                'chapter' => [
                    'regex' => '/\/chapter-[^\.]+\.(\d+)/i'
                ]
            ],
            'options' => [
                'fields' => [
                ],
            ],
            'url' => [
                'host' => 'https://02.ikiru.wtf',
                'latest' => 'https://02.ikiru.wtf/advanced-search/?the_exclude=crossdressing%2Cgender-bender%2Cgenderswap%2Cincest%2Cnsfw%2Cshoujo-ai%2Cshounen-ai%2Cbodyswap&the_page={$page}&orderby=updated&order=desc',
                'search' => 'https://02.ikiru.wtf/advanced-search/?the_exclude=crossdressing%2Cgender-bender%2Cgenderswap%2Cincest%2Cnsfw%2Cshoujo-ai%2Cshounen-ai%2Cbodyswap&the_page={$page}&orderby=popular&order=desc&search_term={$value}',
                'advanced' => 'https://02.ikiru.wtf/advanced-search/?the_exclude=crossdressing%2Cgender-bender%2Cgenderswap%2Cincest%2Cnsfw%2Cshoujo-ai%2Cshounen-ai%2Cbodyswap&the_page={$page}&{$value}',
                'series' => 'https://02.ikiru.wtf/manga/{$slug}/',
                'chapter' => 'https://02.ikiru.wtf/?p={$chapter}',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//a[contains(@href, '/chapter-')]//p",
                ],
            ],
            'LS' => [
                'parent' => "//*[@id='search-results']//a[@color='primary']/ancestor::*[contains(@class, 'group-data-') and contains(@class, '=horizontal')]",
                'title' => [
                    'xpath' => ".//h1",
                ],
                'cover' => [
                    'xpath' => ".//a[@color='primary']//img",
                    'attr' => 'src',
                ],
                'type' => [
                    'xpath' => ".//img[contains(@src, 'static/svg/')]",
                    'attr' => 'src',
                    'regex' => '/[\s\/](man(?:h[wu]|g)a)/i',
                ],
                'completed' => [
                    'xpath' => ".//*[contains(@class, 'rounded-full')]/following-sibling::p",
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
                    'manual' => true,
                    'next' => [
                        'xpath' => "//*[@id='search-results']//*[name()='polyline' and @points='9 18 15 12 9 6']/ancestor::button",
                    ],
                    'prev' => [
                        'xpath' => "//*[@id='search-results']//*[name()='polyline' and @points='15 18 9 12 15 6']/ancestor::button",
                    ],
                ],
            ],
            'series' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//h1[@itemprop='name']",
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//h1[@itemprop='name']/following-sibling::*",
                    'regex' => '/(\s+)?((judul\s)?(alternati[fv]e?|lain)(\stitles?)?)\:?\s+/i',
                ],
                'cover' => [
                    'xpath' => ".//*[@itemprop='image']//img",
                    'attr' => 'src',
                ],
                'detail' => [
                    'type' => [
                        'xpath' => ".//*[contains(text(), 'Type')]/../..//*[contains(@class, 'inline')]/p", //same as parent::
                    ],
                    'released' => [
                        'xpath' => ".//*[contains(text(), 'Released')]/../..//*[contains(@class, 'inline')]/p", //same as parent::
                    ],
                    'genre' => [
                        'xpath' => ".//a[contains(@href, '/genre/')]/parent::*//a",
                    ],
                ],
                'desc' => [
                    'xpath' => ".//*[@itemprop='description' and not(contains(text(),  '[…]'))]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapter-list']//*[@data-chapter-number]//a",
                    'attr' => 'href',
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'fetch' => [
                        'host' => true,
                        'url' => '/wp-admin/admin-ajax.php?action=chapter_list&manga_id={$series_id}&page=1',
                        'xpath' => ".//*[@id='chapter-list']",
                    ],
                    'num' => [ //parent is xpath above
                        'xpath' => "./parent::*",
                        'attr' => 'data-chapter-number',
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//main",
                'title' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]//*[contains(text(), 'hapter')]",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'cover' => [
                    'xpath' => ".//a[contains(@href, '/manga/')]//img",
                    'attr' => 'src',
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[@data-lucide='chevron-right']//ancestor::a[contains(@href, '/manga/')]",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[@data-lucide='chevron-left']//ancestor::a[contains(@href, '/manga/')]",
                        'attr' => 'href',
                    ],
                ],
                'images' => [
                    'xpath' => ".//section/img",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function maid()
    {
        $data = [
            'backend' => 'wordpress',
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komiksin.net',
                'latest' => 'https://komiksin.net/manga/?page={$page}&order=update',
                'search' => 'https://komiksin.net/page/{$page}/?s={$value}',
                'advanced' => 'https://komiksin.net/manga/?page={$page}&{$value}',
                'series' => 'https://komiksin.net/manga/{$slug}/',
                'chapter' => 'https://komiksin.net/{$slug}-chapter-{$chapter}/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://kiryuu02.com',
                'latest' => 'https://kiryuu02.com/manga/?page={$page}&order=update',
                'search' => 'https://kiryuu02.com/page/{$page}/?s={$value}',
                'advanced' => 'https://kiryuu02.com/manga/?page={$page}&{$value}',
                'series' => 'https://kiryuu02.com/manga/{$slug}/',
                'chapter' => 'https://kiryuu02.com/{$slug}-chapter-{$chapter}/',
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
                    'regex' => '/([kc]omi[kc]s?|man(ga|hwa|hua)|series|title)\s/i',
                    'regex2' => '/(\sbahasa?)?\s(\bindo\b|indos?nesiaa?)/i',
                ],
                'alternative' => [
                    'xpath' => ".//*[contains(@class, 'seriestualt')]",
                ],
                'cover' => [
                    'xpath' => ".//*[contains(@class, 'thumb')]//img",
                    'attr' => 'src',
                    'attr_alt' => 'data-lzl-src',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
            'theme' => 'enduser',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komikcast03.com',
                'latest' => 'https://komikcast03.com/daftar-komik/page/{$page}/?sortby=update',
                'search' => 'https://komikcast03.com/page/{$page}/?s={$value}',
                'advanced' => 'https://komikcast03.com/daftar-komik/page/{$page}/?{$value}',
                'series' => 'https://komikcast03.com/komik/{$slug}/',
                'chapter' => 'https://komikcast03.com/chapter/{$slug}-chapter-{$chapter}-bahasa-indonesia/',
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'xpath' => ".//*[contains(@class, 'komik_info-description-sinopsis')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                    'remove' => [
                        ".//*[contains(@class, 'post-views')]",
                    ],
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapter-wrapper']//*[contains(@class, 'komik_info-chapters-item')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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

    public static function klikmanga()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://klikmanga.org',
                'latest' => 'https://klikmanga.org/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://klikmanga.org/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://klikmanga.org/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://klikmanga.org/manga/{$slug}/',
                'chapter' => 'https://klikmanga.org/manga/{$slug}/chapter-{$chapter}/?style=list',
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
                    'xpath' => ".//*[@id='manga-chapters-holder']//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a[not(contains(@class, 'c-new-tag'))]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'fetch' => [
                        'url' => '/ajax/chapters/',
                        'xpath' => ".//*[@id='manga-chapters-holder']",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/manga/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://01.lumosgg.com',
                'latest' => 'https://01.lumosgg.com/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://01.lumosgg.com/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://01.lumosgg.com/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://01.lumosgg.com/komik/{$slug}/',
                'chapter' => 'https://01.lumosgg.com/komik/{$slug}/chapter-{$chapter}/?style=list',
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
                    'xpath' => ".//*[@id='tab-manga-summary']//*[contains(@class, 'post-content_item')]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a[not(contains(@class, 'c-new-tag'))]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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

    public static function cosmicscans()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://lc4.cosmicscans.asia',
                'latest' => 'https://lc4.cosmicscans.asia/manga/?page={$page}&order=update',
                'search' => 'https://lc4.cosmicscans.asia/page/{$page}/?s={$value}',
                'advanced' => 'https://lc4.cosmicscans.asia/manga/?page={$page}&{$value}',
                'series' => 'https://lc4.cosmicscans.asia/manga/{$slug}/',
                'chapter' => 'https://lc4.cosmicscans.asia/{$slug}-chapter-{$chapter}/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://manhwalist02.site',
                'latest' => 'https://manhwalist02.site/manga/?page={$page}&order=update',
                'search' => 'https://manhwalist02.site/page/{$page}/?s={$value}',
                'advanced' => 'https://manhwalist02.site/manga/?page={$page}&{$value}',
                'series' => 'https://manhwalist02.site/manga/{$slug}/',
                'chapter' => 'https://manhwalist02.site/{$slug}-chapter-{$chapter}/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://ainzscans01.com',
                'latest' => 'https://ainzscans01.com/series/?page={$page}&order=update',
                'search' => 'https://ainzscans01.com/page/{$page}/?s={$value}',
                'advanced' => 'https://ainzscans01.com/series/?page={$page}&{$value}',
                'series' => 'https://ainzscans01.com/series/{$slug}/',
                'chapter' => 'https://ainzscans01.com/{$slug}-chapter-{$chapter}/',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//*[contains(@class, 'adds')]//*[contains(@class, 'epxs')]",
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'xpath' => ".//*[contains(@class, 'entry-content') and @itemprop='description']/node()[not(self::div[contains(@class, 'post-views')])]",
                    'regex' => '/\s+|&(#160|nbsp);/',
                ],
                'chapter' => [
                    'xpath' => ".//*[@id='chapterlist']//*[contains(@class, 'eph-num')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'lang' => 'id',
            'parser' => 'api',
            'per_page' => 40,
            'url' => [
                'host' => 'https://westmanga.me',
                'latest' => 'https://westmanga.me/contents?type=Comic&page={$page}&orderBy=Update',
                'default' => 'https://westmanga.me/contents?type=Comic&page={$page}&orderBy=Added',
                'search' => 'https://westmanga.me/contents?type=Comic&page={$page}&q={$value}',
                'advanced' => 'https://westmanga.me/contents?type=Comic&page={$page}&{$value}',
            ],
        ];

        return $data;
    }

    public static function komikstation()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komikstation.org',
                'latest' => 'https://komikstation.org/manga/?page={$page}&order=update',
                'search' => 'https://komikstation.org/page/{$page}/?s={$value}',
                'advanced' => 'https://komikstation.org/manga/?page={$page}&{$value}',
                'series' => 'https://komikstation.org/manga/{$slug}/',
                'chapter' => 'https://komikstation.org/{$slug}-chapter-{$chapter}/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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

    public static function mangakita()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'themesia',
            'lang' => 'id',
            'url' => [
                'host' => 'https://mangakita.id',
                'latest' => 'https://mangakita.id/manga/?page={$page}&order=update',
                'search' => 'https://mangakita.id/page/{$page}/?s={$value}',
                'advanced' => 'https://mangakita.id/manga/?page={$page}&{$value}',
                'series' => 'https://mangakita.id/manga/{$slug}/',
                'chapter' => 'https://mangakita.id/{$slug}-chapter-{$chapter}/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(@class, 'chapternum')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//article",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'allc')]//a",
                ],
                'json' => [ //no parent
                    'xpath' => "//script[contains(text(), 'ts_reader.run')]",
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'parser' => 'api',
            'url' => [
                'host' => 'https://softkomik.com',
                'latest' => 'https://softkomik.com/komik/library?page={$page}&sortBy=new',
                'default' => 'https://softkomik.com/komik/library?page={$page}&sortBy=newKomik',
                'search' => 'https://softkomik.com/komik/list?page={$page}&name={$value}',
                'advanced' => 'https://softkomik.com/komik/library?page={$page}&{$value}',
            ],
        ];

        return $data;
    }

    public static function komiku()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'none',
            'lang' => 'id',
            'url' => [
                'host' => 'https://komiku.org',
                'latest' => 'https://api.komiku.org/manga/page/{$page}/?orderby=modified',
                'search' => 'https://api.komiku.org/manga/page/{$page}/?s={$value}',
                'advanced' => 'https://api.komiku.org/manga/page/{$page}/?{$value}',
                'series' => 'https://komiku.org/manga/{$slug}/',
                'chapter' => 'https://komiku.org/{$slug}-chapter-{$chapter}/',
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/(-(bahasa?-)?(\bindo\b|indos?nesiaa?)\/?|\/([\?#].*)?$)/i',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//span",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//*[@id='main']",
                'title' => [
                    'xpath' => ".//header//h1",
                    'regex' => '/(?:(?:[kc]omi[kc]s?|man(?:ga|hwa|hua)|series|title)\s)?(.*)/i',
                    'regex2' => '/(\s-)?\s(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b\.?).*/i',
                ],
                'nav' => [ //no parent
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => "//*[contains(@class, 'nxpr')]//*[name()='svg' and @data-icon='caret-right']/parent::*",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => "//*[contains(@class, 'nxpr')]//*[name()='svg' and @data-icon='caret-left']/parent::*",
                        'attr' => 'href',
                    ],
                ],
                'images' => [ //no parent
                    'xpath' => "//*[@id='Baca_Komik']//img",
                    'attr' => 'src',
                ],
            ],
        ];

        return $data;
    }

    public static function mgkomik()
    {
        $data = [
            'backend' => 'wordpress',
            'theme' => 'madara',
            'lang' => 'id',
            'url' => [
                'host' => 'https://id.mgkomik.cc',
                'latest' => 'https://id.mgkomik.cc/page/{$page}/?s&post_type=wp-manga&m_orderby=latest',
                'search' => 'https://id.mgkomik.cc/page/{$page}/?post_type=wp-manga&s={$value}',
                'advanced' => 'https://id.mgkomik.cc/page/{$page}/?post_type=wp-manga&{$value}',
                'series' => 'https://id.mgkomik.cc/komik/{$slug}/',
                'chapter' => 'https://id.mgkomik.cc/komik/{$slug}/chapter-{$chapter}/?style=list',
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
                    'regex2' => '/\/([\?#].*)?$/',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'lang' => 'id',
            'parser' => 'api',
            'scid' => [ 'series', 'chapter' ],
            'url' => [
                'host' => 'https://09.shinigami.asia',
                'latest' => 'https://09.shinigami.asia/search',
                'search' => 'https://09.shinigami.asia/search?q={$value}',
                'advanced' => 'https://09.shinigami.asia/search',
            ],
        ];

        return $data;
    }

    public static function webtoons()
    {
        $data = [
            'lang' => 'id',
            'parser' => 'api',
            'per_page' => 30,
            'scid' => [ 'series' ],
            'url' => [
                'host' => 'https://webtoons.com',
                'latest' => 'https://webtoons.com/{$locale}/originals?sortOrder=UPDATE',
                'search' => 'https://webtoons.com/{$locale}/search/originals?page={$page}&keyword={$value}',
            ],
        ];

        return $data;
    }

    public static function leviatanscans()
    {
        $data = [
            'backend' => 'wordpress',
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
                    'xpath' => ".//*[@id='manga-chapters-holder']//*[contains(@class, 'version-chap')]//*[contains(@class, 'wp-manga-chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                    'fetch' => [
                        'url' => '/ajax/chapters/',
                        'xpath' => ".//*[@id='manga-chapters-holder']",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'c-blog-post')]",
                'title' => [
                    'xpath' => ".//*[contains(@class, 'c-breadcrumb')]//a[contains(@href, '/manga/')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(text(), 'Chapter')]/parent::*",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//*[contains(@class, 'reading-content')]",
                'title' => [
                    'xpath' => ".//a[contains(@class, 'manga-title')]",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'wordpress',
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
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
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
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'backend' => 'none',
            'theme' => 'none',
            'lang' => 'en',
            'scid' => [
                'series',
                'chapter' => [
                    'regex' => '/title\/(?:[^\-]+)[^\/]+\/([^\-]+)-/i'
                ]
            ],
            'options' => [
                'headers' => [
                    // language "en" and exclude_nsfw (12), https://i.ibb.co.com/spVVhCgW/mangapark-v20251008.png#Gf55X9dH
                    'Cookie' => 'tfv=1767782464276; bset=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJiaWQiOiI2OTVlMzk2NTI0YTYzZWI5YWVkMDM2YTAiLCJpYXQiOjE3Njc3ODI3NTd9.Zk7wNdeeJePwqtenrl5Kwv1tTuOTwBzvPYBzW-Y7JIQ',
                ],
            ],
            'url' => [
                'host' => 'https://mangapark.to',
                'latest' => 'https://mangapark.to/search?page={$page}&sortby=field_update',
                'search' => 'https://mangapark.to/search?page={$page}&sortby=field_update&word={$value}',
                'advanced' => 'https://mangapark.to/search?page={$page}&{$value}',
                'series' => 'https://mangapark.to/title/{$slug}',
                'chapter' => 'https://mangapark.to/title/{$slug}/{$chapter}',
            ],
            'latest' => [ //parent is same as "LS" parent
                'chapter' => [
                    'xpath' => ".//a[contains(@href, '-chapter-') or contains(@href, '-ch-') or contains(@href, '-episode-') or contains(@href, '-ep-') or contains(@href, '-volume-') or contains(@href, '-vol-') or contains(@href, '-shot-')]",
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
                'parent' => "//main[not(contains(.//h5, 'Error'))]",
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
                    'xpath' => ".//*[@data-name='chapter-list']//a[contains(@href, '-chapter-') or contains(@href, '-ch-') or contains(@href, '-episode-') or contains(@href, '-ep-') or contains(@href, '-volume-') or contains(@href, '-vol-') or contains(@href, '-shot-')]",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                ],
            ],
            'chapter' => [
                'parent' => "//main[not(contains(.//h5, 'Error'))]",
                'title' => [
                    'xpath' => ".//h3//a",
                ],
                'nav' => [
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
                    'next' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='0B_9']]//a[contains(@href, '-chapter-') or contains(@href, '-ch-') or contains(@href, '-episode-') or contains(@href, '-ep-') or contains(@href, '-volume-') or contains(@href, '-vol-') or contains(@href, '-shot-')]//*[@name='angle-right']/parent::*",
                        'attr' => 'href',
                    ],
                    'prev' => [
                        'xpath' => ".//*[@*[name()='q:key' and .='0B_9']]//a[contains(@href, '-chapter-') or contains(@href, '-ch-') or contains(@href, '-episode-') or contains(@href, '-ep-') or contains(@href, '-volume-') or contains(@href, '-vol-') or contains(@href, '-shot-')]//*[@name='angle-left']/parent::*",
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

    public static function weebcentral()
    {
        $data = [
            'backend' => 'none',
            'theme' => 'none',
            'lang' => 'en',
            'per_page' => 32, //32 series per page (default), cannot be changed
            'scid' => [
                'series',
                'chapter' => [
                    'regex' => '/\/chapters?\/(.+)/i'
                ]
            ],
            'url' => [
                'host' => 'https://weebcentral.com',
                'latest' => 'https://weebcentral.com/latest-updates/{$page}',
                'search' => 'https://weebcentral.com/search/data?limit=32&order=Descending&display_mode=Full+Display&excluded_tag=Gender+Bender&excluded_tag=Hentai&excluded_tag=Shoujo+Ai&excluded_tag=Shounen+Ai&excluded_tag=Smut&excluded_tag=Yaoi&excluded_tag=Yuri&offset={$page}&text={$value}',
                'advanced' => 'https://weebcentral.com/search/data?limit=32&order=Descending&display_mode=Full+Display&excluded_tag=Gender+Bender&excluded_tag=Hentai&excluded_tag=Shoujo+Ai&excluded_tag=Shounen+Ai&excluded_tag=Smut&excluded_tag=Yaoi&excluded_tag=Yuri&offset={$page}&{$value}',
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
                    'xpath' => ".//*[@id='chapter-list']//*[contains(@x-data, 'new_chapter')]//a",
                    'regex' => '/(?:-(?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b))?-(\d(?:[\w\-]+)?)$/i',
                    'regex2' => '/\/([\?#].*)?$/',
                    'attr' => 'href',
                    'fetch' => [
                        'url' => '/full-chapter-list',
                        'xpath' => ".//*[@id='chapter-list']",
                    ],
                    'num' => [ //parent is xpath above
                        'xpath' => ".//*[contains(text(), 'Chapter') or contains(text(), 'Episode')]",
                    ],
                ],
            ],
            'chapter' => [
                'parent' => "//main",
                'title' => [
                    'xpath' => ".//a[contains(@href, '/series/')]//span",
                ],
                'nav' => [
                    'fetch' => 'https://weebcentral.com/series/{$slug}/chapter-select?current_chapter={$chapter}',
                    'area' => ".//*[@id='chapter-select-body']", //fetch
                    'regex' => '[\-\/](?:c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|vol(?:ume)?|view|shot|\b(?:ch(?:ap)?|ep)\b)',
                    'regex2' => '\-(?!\bep\b)[^\/\.]*(?:\d+|start|end|fix|e(?:x|ks)tra|spe[cs]ial|sea?s[io]n?|side(?:\-story)?|mentok|raw|tamat|epilog(?:ue)?|s\d|promo|novel)',
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
            'parser' => 'api',
            'per_page' => 49,
            'scid' => [ 'chapter' ],
            'url' => [
                'host' => 'https://comick.dev',
                'latest' => 'https://comick.dev/search?excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri&type=comic&showall=true&page={$page}&limit=49&sort=uploaded',
                'search' => 'https://comick.dev/search?excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri&type=comic&showall=true&page={$page}&limit=49&q={$value}',
                'advanced' => 'https://comick.dev/search?excludes=gender-bender&excludes=shoujo-ai&excludes=shounen-ai&excludes=crossdressing&excludes=genderswap&excludes=incest&excludes=yaoi&excludes=yuri&type=comic&showall=true&page={$page}&limit=49&{$value}',
            ],
        ];

        return $data;
    }
}
