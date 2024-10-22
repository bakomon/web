<?php

namespace Api\Controllers;

require __DIR__ . '/../Services/Http.php';
require __DIR__ . '/../Services/xSelector.php';
require __DIR__ . '/../Services/Webtoons.php';

use \DOMXpath;
use Api\Services\Http;
use Api\Services\xSelector;
use Api\Services\WebtoonsParser;

class Controller
{
    private function param_check(string $name)
    {
        return isset($_GET[$name]) && (!empty($_GET[$name]) || $_GET[$name] != '');
    }

    private function queryX(DOMXPath $xpath, string $query, $contextNode = null) {
        if (strpos($query, '.') === 0 && $contextNode !== null) {
            return $xpath->query($query, $contextNode);
        } else {
            return $xpath->query($query);
        }
    }

    private function keys_exist($keys, $array){
        foreach($keys as $key){
            if(!array_key_exists($key, $array)) return false;
        }
        return true;
    }

    public function latestPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) :
            $ls_exists = false;
            $page = $this->param_check('page') ? $_GET['page'] : '1';

            if ($source_site == 'webtoons') :
                $display = 24;
                $locale = 'id';
                $webtoons = new WebtoonsParser($locale);

                $ls_data = $webtoons->getLatest('update');
                $source_xml = $webtoons->response;
                $source_link = "https://webtoons.com/$locale/originals?sortOrder=UPDATE";
                // $source_link = $source_xml->link;

                if (!isset($ls_data['error'])) :
                    $chunks = array_chunk($ls_data, $display); //split array

                    $ls_lists = $chunks[(int)$page - 1] ?? [];
                    $ls_exists = count($ls_lists) > 0;

                    $next_page = (int)$page + 1;
                    $next = isset($chunks[$next_page]) ? (string)$next_page : '';
                    $prev = $page == '1' ? '' : (int)$page - 1;
                    $prev = (string)$prev;
                endif;
            else :
                $source = xSelector::$source_site();
                $source_link = str_replace('{$page}', $page, $source['url']['latest']);
                $source_xml = Http::load($source_link);
                $xpath = new DOMXpath($source_xml->responseParse());

                if ($source_xml->isBlocked($xpath)) :
                    $source_xml = Http::bypass($source_link);
                    if ($source_xml->isSuccess()) :
                        $xpath = new DOMXpath($source_xml->responseParse());
                    else :
                        return (object) $source_xml->showError();
                    endif;
                else :
                    if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                endif;

                if ($source_xml->isDomainChanged($xpath)) :
                    $source_xml->status = 0;
                    return (object) $source_xml->showError("Domain Changed ($source_site)");
                endif;

                $lists = $xpath->query($source['LS']['parent']); //parent
                $ls_exists = $lists->length > 0;
                if ($ls_exists) :
                    $ls_lists = [];

                    foreach ($lists as $index) {
                        if (array_key_exists('type', $source['LS'])) :
                            $type_el = $this->queryX($xpath, $source['LS']['type']['xpath'], $index);
                            if ($this->keys_exist(['attr', 'regex'], $source['LS']['type'])) :
                                $type_el = $type_el->length > 0 ? $type_el[0]->getAttribute($source['LS']['type']['attr']) : '';
                                $type_chk = preg_match($source['LS']['type']['regex'], $type_el, $type);
                                $type = $type_chk ? strtolower($type[1]) : '';
                            else :
                                $type = $type_el->length > 0 ? strtolower($type_el[0]->textContent) : '';
                            endif;
                        else :
                            $type = '';
                        endif;

                        if (array_key_exists('color', $source['LS'])) :
                            $color = $this->queryX($xpath, $source['LS']['color']['xpath'], $index);
                            $color = $color->length > 0 ? true : false;
                        else :
                            $color = '';
                        endif;

                        if (array_key_exists('completed', $source['LS'])) :
                            $completed_el = $this->queryX($xpath, $source['LS']['completed']['xpath'], $index);
                            if ($completed_el->length > 0) :
                                $completed_str = $source['theme'] == 'madara' ? trim($completed_el[0]->textContent) : $completed_el[0]->getAttribute($source['LS']['completed']['attr']);
                            else :
                                $completed_str = '';
                            endif;
                            $completed = preg_match($source['LS']['completed']['regex'], $completed_str);
                        else :
                            $completed = false;
                        endif;

                        if (array_key_exists('date', $source['latest'])) :
                            $date = $this->queryX($xpath, $source['latest']['date']['xpath'], $index);
                            $date = $date->length > 0 ? trim($date[0]->textContent) : '';
                        else :
                            $date = '';
                        endif;

                        $title = trim($this->queryX($xpath, $source['LS']['title']['xpath'], $index)[0]->textContent);

                        $cover = $this->queryX($xpath, $source['LS']['cover']['xpath'], $index);
                        if ($cover->length > 0) :
                            $attr_alt = array_key_exists('attr_alt', $source['LS']['cover']) && $cover[0]->hasAttribute($source['LS']['cover']['attr_alt']);
                            $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                            $cover = $cover[0]->getAttribute($source['LS']['cover'][$cover_attr]);
                        else :
                            $cover = '';
                        endif;

                        // NSFW
                        if ($source_site == 'maid' && preg_match('/\sdoujin/i', $type_el)) continue;

                        $chapter = $this->queryX($xpath, $source['latest']['chapter']['xpath'], $index);
                        if ($chapter->length > 0) :
                            $chapter = preg_replace('/c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b\.?[\s\t]+/i', '', trim($chapter[0]->textContent));
                            $chapter = preg_replace('/' . preg_quote($title, '/') . '[\s\t]+/i', '', $chapter);
                            if (array_key_exists('regex', $source['latest']['chapter'])) $chapter = preg_replace($source['latest']['chapter']['regex'], '', $chapter);
                            $chapter = preg_replace('/\s?\t+/', "\x20", $chapter);
                        else :
                            $chapter = '';
                        endif;

                        preg_match($source['LS']['slug']['regex'], $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['slug']['attr']), $slug);

                        array_push($ls_lists, [
                            'title' => $title,
                            'cover' => $cover,
                            'type' => $type,
                            'color' => $color,
                            'completed' => $completed,
                            'chapter' => trim($chapter),
                            'date' => $date,
                            'url' => $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['link']['attr']),
                            'slug' => preg_replace($source['LS']['slug']['regex2'], '', $slug[1]),
                        ]);
                    }

                    if (($source['theme'] == 'madara' || $source_site == 'komiku') && array_key_exists('ajax', $source['LS']['nav'])) :
                        $next_page = (int)$page + 1;
                        $next_link = preg_replace('/([\?\/]page[=\/])\d+/', '${1}' . $next_page, $source_link);
                        $next_xml = Http::load($next_link);
                        $next = $next_xml->getStatus() == '404' ? '' : (string)$next_page;

                        $prev = $page == '1' ? '' : (int)$page - 1;
                        $prev = (string)$prev;
                    else :
                        $n_pattern = $source['LS']['nav']['regex'];
                        $next_btn = $this->queryX($xpath, $source['LS']['nav']['next']['xpath'], $lists);
                        $prev_btn = $this->queryX($xpath, $source['LS']['nav']['prev']['xpath'], $lists);

                        // next button
                        if ($next_btn->length > 0) :
                            preg_match($n_pattern, $next_btn[0]->getAttribute($source['LS']['nav']['next']['attr']), $next);
                            $next = $next[1];
                        else :
                            $next = '';
                        endif;

                        // prev button
                        if ($prev_btn->length > 0) :
                            $prev_index = $source['theme'] == 'madara' && $prev_btn->length > 1 ? ($prev_btn->length - 1) : 0;
                            preg_match($n_pattern, $prev_btn[$prev_index]->getAttribute($source['LS']['nav']['prev']['attr']), $prev);
                            $prev = empty($prev) ? '1' : $prev[1];
                        else :
                            $prev = '';
                        endif;
                    endif;
                endif;
            endif;

            if ($ls_exists) :
                $data = [
                    'status' => 'SUCCESS',
                    'status_code' => $source_xml->getStatus(),
                    'cache' => $source_xml->cache,
                    'bypass' => $source_xml->bypass,
                    'next' => $next,
                    'prev' => $prev,
                    'source' => $source_link,
                    'lists' => $ls_lists,
                ];
            else :
                $data = [
                    'status' => 'NOT_FOUND',
                    'status_code' => 404,
                    'cache' => $source_xml->cache,
                    'bypass' => $source_xml->bypass,
                    'message' => 'Not Found',
                    'source' => $source_link,
                ];
            endif;

            return (object) $data;
        else :
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        endif;
    }

    public function searchPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) :
            $data = [];
            $querams = $this->param_check('params') ? 'params' : 'query';
            $value = isset($_GET[$querams]) && (!empty($_GET[$querams]) || $_GET[$querams] === '0') ?  $_GET[$querams] : null;

            if (!$value && $value !== '0') :
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            else :
                $sc_exists = false;
                $page = $this->param_check('page') ? $_GET['page'] : '1';
                $is_advanced = $this->param_check('params') ? true : false;
                $val = $is_advanced ? $value : rawurlencode($value);

                if ($source_site == 'webtoons') :
                    $locale = 'id';
                    $webtoons = new WebtoonsParser($locale);

                    if ($value == 'default') {
                        $display = 24;
                        $sc_data = $webtoons->getLatest('added');
                        $source_link = "https://m.webtoons.com/$locale/new";
                        $chunks = array_chunk($sc_data, $display); //split array
                        $sc_lists = $chunks[(int)$page - 1] ?? [];
                    } else {
                        $sc_data = $webtoons->getSearch($value, $page);
                        $source_link = "https://webtoons.com/$locale/search?searchType=WEBTOON&page=$page&keyword=$value";
                        $sc_lists = $sc_data['list'];
                        $chunks = array_chunk(range(1, $sc_data['total']), $sc_data['display']);
                    }
                    $source_xml = $webtoons->response;
                    // $source_link = $source_xml->link;
                    $sc_exists = count($sc_lists) > 0;

                    $next_page = (int)$page + 1;
                    $next = isset($chunks[$next_page]) ? (string)$next_page : '';
                    $prev = $page == '1' ? '' : (int)$page - 1;
                    $prev = (string)$prev;
                else :
                    $source = xSelector::$source_site();

                    $qs = $is_advanced && $source['theme'] == 'eastheme' ? '?' : '';
                    $search = ['{$page}', '{$value}'];
                    $replace = $value == 'default' ? [$page, ''] : [$page, $qs . $val];
                    $full_url = $is_advanced ? 'advanced' : 'search';
                    $source_link = str_replace($search, $replace, $source['url'][$full_url]);

                    if ($is_advanced && $value == 'default') $source_link = preg_replace('/[\?&]=?$/', '', $source_link);
                    if ($source['theme'] == 'madara') :
                        if (strpos($source_link, '&s=') === FALSE) $source_link .= '&s';
                        if (strpos($source_link, '&type=') !== FALSE) $source_link = str_replace('&type', '&genre[]', $source_link);
                    endif;
                    if (strpos($value, 'order=') === FALSE && ($source['theme'] == 'koidezign' || $is_advanced && $value == 'default')) :
                        $s_qs = strpos($source_link, '?') !== FALSE ? '&' :'?';
                        $s_value = $source['theme'] == 'enduser' ? 'update' : ($source_site == 'komiku' ? 'date' : 'latest'); //order/sort by "added (latest)"
                        $s_url = $source['theme'] == 'madara' ? 'm_orderby=new-manga' : ($source_site == 'komiku' ? 'orderby=' : 'order=') . $s_value;
                        $source_link .= $s_qs . $s_url;
                    endif;

                    $source_xml = Http::load($source_link);
                    $xpath = new DOMXpath($source_xml->responseParse());

                    if ($source_xml->isBlocked($xpath)) :
                        $source_xml = Http::bypass($source_link);
                        if ($source_xml->isSuccess()) :
                            $xpath = new DOMXpath($source_xml->responseParse());
                        else :
                            return (object) $source_xml->showError();
                        endif;
                    else :
                        if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                    endif;

                    if ($source_xml->isDomainChanged($xpath)) :
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Domain Changed ($source_site)");
                    endif;

                    $sc_path = array_key_exists('search', $source) ? 'search' : 'LS';

                    $ls_path = array_key_exists('parent', $source[$sc_path]) ? $sc_path : 'LS';
                    $lists = $xpath->query($source[$ls_path]['parent']); //parent
                    $sc_lists = [];
                    $sc_exists = $lists->length > 0;

                    if ($sc_exists) :
                        foreach ($lists as $index) {
                            if (array_key_exists('type', $source['LS'])) :
                                $type_el = $this->queryX($xpath, $source['LS']['type']['xpath'], $index);
                                if ($this->keys_exist(['attr', 'regex'], $source['LS']['type'])) :
                                    $type_el = $type_el->length > 0 ? $type_el[0]->getAttribute($source['LS']['type']['attr']) : '';
                                    $type_chk = preg_match($source['LS']['type']['regex'], $type_el, $type);
                                    $type = $type_chk ? strtolower($type[1]) : '';
                                else :
                                    $type = $type_el->length > 0 ? strtolower($type_el[0]->textContent) : '';
                                endif;
                            else :
                                $type = '';
                            endif;

                            if (array_key_exists('color', $source['LS'])) :
                                $color = $this->queryX($xpath, $source['LS']['color']['xpath'], $index);
                                $color = $color->length > 0 ? true : false;
                            else :
                                $color = '';
                            endif;

                            if (array_key_exists('completed', $source['LS'])) :
                                $completed_el = $this->queryX($xpath, $source['LS']['completed']['xpath'], $index);
                                if ($completed_el->length > 0) :
                                    $completed_str = $source['theme'] == 'madara' ? trim($completed_el[0]->textContent) : $completed_el[0]->getAttribute($source['LS']['completed']['attr']);
                                else :
                                    $completed_str = '';
                                endif;
                                $completed = preg_match($source['LS']['completed']['regex'], $completed_str);
                            else :
                                $completed = false;
                            endif;

                            $t_path = array_key_exists('title', $source[$sc_path]) ? $sc_path : 'LS';
                            if ($source['theme'] == 'koidezign') :
                                $title = $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source[$t_path]['title']['attr']);
                            else :
                                $title = $this->queryX($xpath, $source[$t_path]['title']['xpath'], $index)[0]->textContent;
                            endif;

                            $cover = $this->queryX($xpath, $source['LS']['cover']['xpath'], $index);
                            if ($cover->length > 0) :
                                $attr_alt = array_key_exists('attr_alt', $source['LS']['cover']) && $cover[0]->hasAttribute($source['LS']['cover']['attr_alt']);
                                $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                                $cover = $cover[0]->getAttribute($source['LS']['cover'][$cover_attr]);
                            else :
                                $cover = '';
                            endif;

                            // NSFW
                            if ($source_site == 'maid' && preg_match('/\sdoujin/i', $type_el)) continue;

                            preg_match($source['LS']['slug']['regex'], $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['slug']['attr']), $slug);

                            array_push($sc_lists, [
                                'title' => trim($title),
                                'cover' => $cover,
                                'type' => $type,
                                'color' => $color,
                                'completed' => $completed,
                                'url' => $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['link']['attr']),
                                'slug' => preg_replace($source['LS']['slug']['regex2'], '', $slug[1]),
                            ]);
                        }

                        $np_path = array_key_exists('nav', $source[$sc_path]) && !$is_advanced ? $sc_path : 'LS';
                        if (($source['theme'] == 'madara' || $source_site == 'komiku') && array_key_exists('ajax', $source[$np_path]['nav'])) :
                            $next_page = (int)$page + 1;
                            $next_link = preg_replace('/([\?\/]page[=\/])\d+/', '${1}' . $next_page, $source_link);
                            $next_xml = Http::load($next_link);
                            $next = $next_xml->getStatus() == '404' ? '' : (string)$next_page;

                            $prev = $page == '1' ? '' : (int)$page - 1;
                            $prev = (string)$prev;
                        else :
                            $n_pattern = $source[$np_path]['nav']['regex'];
                            $next_btn = $this->queryX($xpath, $source[$np_path]['nav']['next']['xpath'], $lists);
                            $prev_btn = $this->queryX($xpath, $source[$np_path]['nav']['prev']['xpath'], $lists);

                            // next button
                            if ($next_btn->length > 0) :
                                preg_match($n_pattern, $next_btn[0]->getAttribute($source[$np_path]['nav']['next']['attr']), $next);
                                $next = $next[1];
                            else :
                                $next = '';
                            endif;

                            // prev button
                            if ($prev_btn->length > 0) :
                                $prev_index = $source['theme'] == 'madara' && $prev_btn->length > 1 ? ($prev_btn->length - 1) : 0;
                                preg_match($n_pattern, $prev_btn[$prev_index]->getAttribute($source[$np_path]['nav']['prev']['attr']), $prev);
                                $prev = empty($prev) ? '1' : $prev[1];
                            else :
                                $prev = '';
                            endif;
                        endif;
                    else :
                        // echo '{$lists} Not Found. $data = ';
                        $next = '';
                        $prev = '';
                    endif;
                endif;

                $data = [
                    'status' => 'SUCCESS',
                    'status_code' => $source_xml->getStatus(),
                    'cache' => $source_xml->cache,
                    'bypass' => $source_xml->bypass,
                    'next' => $next,
                    'prev' => $prev,
                    'source' => $source_link,
                    'lists' => $sc_lists,
                ];
            endif;

            return (object) $data;
        else :
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        endif;
    }

    public function seriesPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) :
            $data = [];
            $slug = $this->param_check('slug') ? $_GET['slug'] : null;
            $title_no = $this->param_check('titleNo') ? $_GET['titleNo'] : null;

            if (!$slug || ($source_site == 'webtoons' && !$title_no)) :
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            else :
                $sr_exists = false;

                if ($source_site == 'webtoons') :
                    $locale = 'id';
                    $webtoons = new WebtoonsParser($locale);

                    $sr_data = $webtoons->getSeries($title_no);
                    $source_xml = $webtoons->response;
                    $sr_exists = !isset($sr_data['error']);

                    if ($sr_exists) :
                        $res_data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'slug' => $slug,
                            'titleNo' => $title_no,
                        ];

                        $data = array_merge($res_data, $sr_data);
                    endif;
                else :
                    $source = xSelector::$source_site();
                    $source_link = str_replace('{$slug}', $slug, $source['url']['series']);
                    $source_xml = Http::load($source_link);
                    $dom = $source_xml->responseParse();
                    $xpath = new DOMXpath($dom);

                    if ($source_xml->isBlocked($xpath)) :
                        $source_xml = Http::bypass($source_link);
                        if ($source_xml->isSuccess()) :
                            $dom = $source_xml->responseParse();
                            $xpath = new DOMXpath($dom);
                        else :
                            return (object) $source_xml->showError();
                        endif;
                    else :
                        if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                    endif;

                    if ($source_xml->isDomainChanged($xpath)) :
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Domain Changed ($source_site)");
                    endif;

                    if (array_key_exists('shortlink', $source['series'])) :
                        $slink = $xpath->query($source['series']['shortlink']['xpath']);
                        if ($slink->length > 0) :
                            preg_match($source['series']['shortlink']['regex'], $slink[0]->getAttribute($source['series']['shortlink']['attr']), $shortlink);
                            $slink = $shortlink[1];
                        else :
                            $slink = $xpath->query("//article[contains(@id, 'post-')]");
                            if ($slink->length > 0) :
                                $slink = preg_replace('/post-(.*)/i', '', $slink[0]->getAttribute('id'));
                            else :
                                $slink = '';
                            endif;
                        endif;
                    else :
                        $slink = '';
                    endif;

                    $article = $xpath->query($source['series']['parent']); //parent
                    $sr_exists = $article->length > 0;
                    if ($sr_exists) :
                        $article = $article[0];

                        $detail = $source['series']['detail'];
                        $genres = $xpath->query($detail['genre']['xpath'], $article);
                        $gr_lists = [];

                        if ($genres->length > 0) :
                            foreach ($genres as $index) {
                                array_push($gr_lists, $index->textContent);
                            }
                        endif;

                        $title = $xpath->query($source['series']['title']['xpath'], $article)[0]->textContent;
                        $title = preg_replace($source['series']['title']['regex'], '', $title);
                        if (isset($source['series']['title']['regex2'])) $title = preg_replace($source['series']['title']['regex2'], '', $title);

                        if (array_key_exists('alternative', $source['series'])) :
                            $alternative = $xpath->query($source['series']['alternative']['xpath'], $article);
                            $alternative = $alternative->length > 0 ? trim($alternative[0]->textContent) : '';
                            if (array_key_exists('regex', $source['series']['alternative'])) :
                                $alternative = preg_replace($source['series']['alternative']['regex'], '', $alternative);
                            endif;
                        else :
                            $alternative = '';
                        endif;

                        $cover = $xpath->query($source['series']['cover']['xpath'], $article);
                        if ($cover->length > 0) :
                            $attr_alt = array_key_exists('attr_alt', $source['series']['cover']) && $cover[0]->hasAttribute($source['series']['cover']['attr_alt']);
                            $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                            $cover = $cover[0]->getAttribute($source['series']['cover'][$cover_attr]);
                        else :
                            $cover = '';
                        endif;

                        if (array_key_exists('type', $detail)) :
                            $type_el = $xpath->query($detail['type']['xpath'], $article);
                            $type = $type_el->length > 0 ? $type_el[0]->textContent : '';
                        else :
                            $type = '';
                        endif;

                        if (array_key_exists('status', $detail)) {
                            $status = $xpath->query($detail['status']['xpath'], $article)[0]->textContent;
                            if (array_key_exists('regex', $detail['status'])) $status = preg_replace($detail['status']['regex'], '', $status);
                        } else {
                            $status = '';
                        }

                        if (array_key_exists('author', $detail)) {
                            $author = $xpath->query($detail['author']['xpath'], $article);
                            $author = $author->length > 0 ? $author[0]->textContent : '';
                            if (array_key_exists('regex', $detail['author'])) $author = preg_replace($detail['author']['regex'], '', $author);
                        } else {
                            $author = '';
                        }

                        if (array_key_exists('artist', $detail)) {
                            $artist = $xpath->query($detail['artist']['xpath'], $article);
                            $artist = $artist->length > 0 ? $artist[0]->textContent : '';
                            if (array_key_exists('regex', $detail['artist'])) $artist = preg_replace($detail['artist']['regex'], '', $artist);
                        } else {
                            $artist = '';
                        }

                        $detail_list = [
                            'type' => strtolower(trim($type)),
                            'status' => trim($status),
                            'author' => trim($author),
                            'artist' => trim($artist),
                            'genre' => implode(', ', $gr_lists),
                        ];

                        $desc = $xpath->query($source['series']['desc']['xpath'], $article);
                        $desc = $desc->length > 0 ? $desc[0]->textContent : '';

                        $ajax_check = array_key_exists('ajax', $source['series']['chapter']) && $xpath->query($source['series']['chapter']['parent'], $article)->length > 0;
                        if ($source['theme'] == 'madara' && $ajax_check) :
                            $chapters_link = $source_link . $source['series']['chapter']['ajax'];
                            $chapters_xml = Http::load($chapters_link, ['method' => 'POST']);

                            if (!$chapters_xml->isSuccess() && $chapters_xml->isBlocked()) $chapters_xml = Http::bypass($chapters_link, true);

                            if ($chapters_xml->isSuccess()) {
                                $chapter_list = $dom->createDocumentFragment();
                                $chapter_list->appendXML($chapters_xml->response());
                                $xpath->query($source['series']['chapter']['parent'], $article)[0]->appendChild($chapter_list);
                            }
                        endif;

                        $chapters = $xpath->query($source['series']['chapter']['xpath'], $article);
                        $ch_lists = [];

                        if ($chapters->length > 0) :
                            foreach ($chapters as $index) {
                                if ($source['theme'] == 'koidezign') :
                                    $date = $xpath->query("//*[contains(@class, 'date')]", $index)[0];
                                    $date->parentNode->removeChild($date);
                                endif;
                                $ch_url = $index->getAttribute($source['series']['chapter']['attr']);

                                $ch_el = array_key_exists('num', $source['series']['chapter']) ? $xpath->query($source['series']['chapter']['num'], $index)[0] : $index;
                                $ch_num = preg_replace('/c(?:[ha](?:ap|hp|pa|a|p))[tp]er|\bch(?:ap)?\b\.?[\s\t]+/i', '', $ch_el->textContent);
                                $ch_num = preg_replace('/' . preg_quote($title, '/') . '[\s\t]+/i', '', $ch_num);
                                if (array_key_exists('regex2', $source['series']['title'])) $ch_num = preg_replace($source['series']['title']['regex2'], '', $ch_num);

                                $sr_slug = $slink ? preg_replace('/^' . $slink . '(\d+)?\-/i', '', $slug) : $slug; //remove shortlink
                                $sr_slug = preg_replace('/s\-/i', 's?-', $sr_slug); //eg. https://regexr.com/7es39
                                $ch_str = preg_replace('/' . $sr_slug . '/i', '', $ch_url);
                                $ch_str = preg_replace($source['series']['chapter']['regex2'], '', $ch_str);
                                preg_match($source['series']['chapter']['regex'], $ch_str, $chapter);

                                $ch_data = [
                                    'number' => $ch_num != '' ? trim($ch_num) : (count($chapter) > 0 ? $chapter[1] : ''),
                                    'url' => parse_url($ch_url, PHP_URL_PATH) . ($source['theme'] == 'madara' ? '?style=list' : ''),
                                ];
                                array_push($ch_lists, $ch_data);
                            }
                        endif;

                        $data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'title' => trim($title),
                            'alternative' => $alternative,
                            'slug' => $slug,
                            'cover' => $cover,
                            'detail' => $detail_list,
                            'desc' => trim(preg_replace($source['series']['desc']['regex'], "\x20", strip_tags($desc))),
                            'source' => $source_link,
                            'chapter' => $ch_lists,
                        ];
                    endif;
                endif;

                if (!$sr_exists) :
                    $data = [
                        'status' => 'NOT_FOUND',
                        'status_code' => 404,
                        'cache' => $source_xml->cache,
                        'bypass' => $source_xml->bypass,
                        'message' => 'Not Found',
                        'source' => $source_link,
                    ];
                endif;
            endif;

            return (object) $data;
        else :
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        endif;
    }

    public function chapterPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) :
            $data = [];
            $slug = $this->param_check('slug') ? $_GET['slug'] : null;
            $chapter = isset($_GET['chapter']) && (!empty($_GET['chapter']) || $_GET['chapter'] === '0') ? $_GET['chapter'] : null;
            $title_no = $this->param_check('titleNo') ? $_GET['titleNo'] : null;

            if (!$slug || (!$chapter && $chapter !== '0') || ($source_site == 'webtoons' && !$title_no)) :
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            else :
                $ch_exists = false;

                if ($source_site == 'webtoons') :
                    $locale = 'id';
                    $webtoons = new WebtoonsParser($locale);
                    $title_no = $this->param_check('titleNo') ? $_GET['titleNo'] : null;

                    $ch_data = $webtoons->getChapter($title_no, $chapter);
                    $source_xml = $webtoons->response;
                    $source_link = "https://webtoons.com/$locale/originals/a/e/viewer?title_no=$title_no&episode_no=$chapter";
                    // $source_link = $source_xml->link;
                    $ch_data['source'] = $source_link;
                    $ch_exists = !isset($ch_data['error']);

                    if ($ch_exists) :
                        $res_data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'titleNo' => $title_no,
                            'title' => '',
                            'slug' => $slug,
                        ];

                        $data = array_merge($res_data, $ch_data);
                    endif;
                else :
                    $source = xSelector::$source_site();
                    $url = $this->param_check('url') ? $_GET['url'] : null;

                    $search = ['{$slug}', '{$chapter}'];
                    $replace = [$slug, $chapter];
                    $source_link = $url ? $source['url']['host'] . $url : str_replace($search, $replace, $source['url']['chapter']);
                    $source_xml = Http::load($source_link);
                    $xpath = new DOMXpath($source_xml->responseParse());

                    if ($source_xml->isBlocked($xpath)) :
                        $source_xml = Http::bypass($source_link);
                        if ($source_xml->isSuccess()) :
                            $xpath = new DOMXpath($source_xml->responseParse());
                        else :
                            return (object) $source_xml->showError();
                        endif;
                    else :
                        if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                    endif;

                    if ($source_xml->isDomainChanged($xpath)) :
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Domain Changed ($source_site)");
                    endif;

                    $content = $xpath->query($source['chapter']['parent']); //parent
                    $ch_exists = $content->length > 0;
                    if ($ch_exists) :
                        $content = $content[0];
                        $img_lists = [];

                        $title_path = $source['chapter']['title']['xpath'];
                        if ($this->keys_exist(['regex', 'regex2'], $source['chapter']['title'])) {
                            $title = $this->queryX($xpath, $title_path, $content)[0]->textContent;
                            $title = preg_replace($source['chapter']['title']['regex2'], '', $title);
                            if (preg_match($source['chapter']['title']['regex'], $title, $m_title)) $title = $m_title[1];
                        } else {
                            if (array_key_exists('slug', $source['chapter']['title'])) $title_path = $title_path . "//a[contains(@href, '$slug')]";
                            $title = $this->queryX($xpath, $title_path, $content)[0]->textContent;
                        }

                        if ($source['theme'] == 'themesia' && !array_key_exists('xpath', $source['chapter']['images'])) :
                            $cover = '';

                            $ch_script = $this->queryX($xpath, $source['chapter']['json']['xpath'], $content);
                            if ($ch_script->length > 0) :
                                preg_match('/(\{[^;]+)\);?/', $ch_script[0]->textContent, $ts_reader);
                                $ch_data = str_replace('!0', 'true', $ts_reader[1]);
                                $ch_data = str_replace('!1', 'false', $ch_data);
                                $ch_data = json_decode($ch_data, true);

                                $nav_rgx1 = $source['chapter']['json']['regex'];
                                $nav_rgx2 = $source['chapter']['json']['regex2'];

                                $next_url = $ch_data[$source['chapter']['json']['next']['name']];
                                if ($next_url != '') :
                                    preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $next_url, $next_str);
                                    $next_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $next_str[0]);
                                    $next_str = str_replace($slug, '', $next_str);
                                    $next = [
                                        'number' => $next_str,
                                        'url' => parse_url($next_url, PHP_URL_PATH),
                                    ];
                                else :
                                    $next = json_decode('{}');
                                endif;

                                $prev_url = $ch_data[$source['chapter']['json']['prev']['name']];
                                if ($prev_url != '') :
                                    preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $prev_url, $prev_str);
                                    $prev_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $prev_str[0]);
                                    $prev_str = str_replace($slug, '', $prev_str);
                                    $prev = [
                                        'number' => $prev_str,
                                        'url' => parse_url($prev_url, PHP_URL_PATH),
                                    ];
                                else :
                                    $prev = json_decode('{}');
                                endif;

                                $images = $ch_data['sources'][0]['images']; //"sources" & "images" from ts_reader

                                if (count($images) > 0) :
                                    foreach ($images as $img) {
                                        array_push($img_lists, preg_replace('/\s/', '%20', $img));
                                    }
                                endif;
                            else :
                                echo '"ts_reader" not found.';
                            endif;
                        else :
                            if (array_key_exists('cover', $source['chapter'])) :
                                $cover = $this->queryX($xpath, $source['chapter']['cover']['xpath'], $content);
                                if ($cover->length > 0) :
                                    $attr_alt = array_key_exists('attr_alt', $source['chapter']['cover']) && $cover[0]->hasAttribute($source['chapter']['cover']['attr_alt']);
                                    $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                                    $cover = $cover[0]->getAttribute($source['chapter']['cover'][$cover_attr]);
                                else :
                                    $cover = '';
                                endif;
                            else :
                                $cover = '';
                            endif;

                            $nav_rgx1 = $source['chapter']['nav']['regex'];
                            $nav_rgx2 = $source['chapter']['nav']['regex2'];

                            $next_btn = $this->queryX($xpath, $source['chapter']['nav']['next']['xpath'], $content);
                            if ($next_btn->length > 0) :
                                $next_url = $next_btn[0]->getAttribute($source['chapter']['nav']['next']['attr']);
                                preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $next_url, $next_str);
                                $next_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $next_str[0]);
                                $next_str = str_replace($slug, '', $next_str);
                                $next = [
                                    'number' => $next_str,
                                    'url' => parse_url($next_url, PHP_URL_PATH) . ($source['theme'] == 'madara' ? '?style=list' : ''),
                                ];
                            else :
                                $next = json_decode('{}');
                            endif;

                            $prev_btn = $this->queryX($xpath, $source['chapter']['nav']['prev']['xpath'], $content);
                            if ($prev_btn->length > 0) :
                                $prev_url = $prev_btn[0]->getAttribute($source['chapter']['nav']['prev']['attr']);
                                preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $prev_url, $prev_str);
                                $prev_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $prev_str[0]);
                                $prev_str = str_replace($slug, '', $prev_str);
                                $prev = [
                                    'number' => $prev_str,
                                    'url' => parse_url($prev_url, PHP_URL_PATH) . ($source['theme'] == 'madara' ? '?style=list' : ''),
                                ];
                            else :
                                $prev = json_decode('{}');
                            endif;

                            $images = $this->queryX($xpath, $source['chapter']['images']['xpath'], $content);

                            if ($images->length > 0) :
                                foreach ($images as $img) {
                                    $attr_alt = array_key_exists('attr_alt', $source['chapter']['images']) && $img->hasAttribute($source['chapter']['images']['attr_alt']);
                                    $img_attr = $attr_alt ? 'attr_alt' : 'attr';
                                    array_push($img_lists, trim($img->getAttribute($source['chapter']['images'][$img_attr])));
                                }
                            endif;
                        endif;

                        $data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'title' => trim($title),
                            'slug' => $slug,
                            'cover' => $cover,
                            'current' => $chapter,
                            'next' => $next,
                            'prev' => $prev,
                            'source' => $source_link,
                            'images' => $img_lists,
                        ];
                    endif;
                endif;

                if (!$ch_exists) :
                    $data = [
                        'status' => 'NOT_FOUND',
                        'status_code' => 404,
                        'cache' => $source_xml->cache,
                        'bypass' => $source_xml->bypass,
                        'message' => 'Not Found',
                        'source' => $source_link,
                    ];
                endif;
            endif;

            return (object) $data;
        else :
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        endif;
    }
}
