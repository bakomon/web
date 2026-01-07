<?php

namespace Api\Controllers;

require dirname(__DIR__) . '/Services/Http.php';
require dirname(__DIR__) . '/Services/xSelector.php';

use \DOMXpath;
use Api\Services\Http;
use Api\Services\xSelector;

class Controller
{
    private $rgx_vol = '(vol(ume)?(\s+)?([\.\:\s]+)?\d+\s+)?';
    private $rgx_ch = '(c([ha](ap|ao|hp|pa|a|p))[tp]er|episode|vol(ume)?|view|\b(ch(ap)?|ep)\b)(\s+)?([\.\:\s]+)?';

    private function param_check(string $name)
    {
        return isset($_GET[$name]) && (!empty($_GET[$name]) || $_GET[$name] != '');
    }

    private function weebcentral_page($type, $op, $per_page)
    {
        if ($type == 'offset') return ($op - 1) * $per_page;
        if ($type == 'page') return intval($op / $per_page) + 1;
    }

    private function is_missing_host($url) {
        $parts = parse_url($url);
        return !isset($parts['host']);
    }

    private function get_host($url) {
        $parts = parse_url($url);
        if (!isset($parts['host'])) return null;

        $scheme = $parts['scheme'] ?? 'http';
        return $scheme . '://' . $parts['host'];
    }

    private function is_scid($page, $source)
    {
        if (array_key_exists('scid', $source)) {
            if (in_array($page, $source['scid']) || array_key_exists($page, $source['scid'])) return true;
        }
        return false;
    }

    private function add_scid($new, $position, $data)
    {
        $start = array_slice($data, 0, $position, true);
        $end = array_slice($data, $position, null, true);
        return $start + $new + $end;
    }

    private function keys_exist($keys, $array)
    {
        foreach($keys as $key){
            if (!array_key_exists($key, $array)) return false;
        }
        return true;
    }

    private function queryX(DOMXPath $xpath, string $query, $contextNode = null)
    {
        if (strpos($query, '.') === 0 && $contextNode !== null) {
            return $xpath->query($query, $contextNode);
        } else {
            return $xpath->query($query);
        }
    }

    private function search_path($child, $source, $advanced = false) {
        $s_path = ['search', 'LS'];
        if ($advanced) array_unshift($s_path, 'advanced');
        foreach ($s_path as $key) {
            if (array_key_exists($key, $source) && array_key_exists($child, $source[$key])) {
                if ($child == 'nav' && $source[$key][$child] == 'LS' && $key != 'LS') return $source['LS'][$child];
                return $source[$key][$child];
            }
        }
        return null;
    }

    private function series_detail($xpath, $param, $detail, $parent) {
        if (array_key_exists($param, $detail)) {
            $data_el = $xpath->query($detail[$param]['xpath'], $parent);
            $data = $data_el->length > 0 ? $data_el[0]->textContent : '';
            if (array_key_exists('regex', $detail[$param])) $data = preg_replace($detail[$param]['regex'], '', $data);
            $data = trim($data);
            return $data == '-' ? '' : $data;
        }
        return '';
    }

    private function chapter_source($source, $url, $data)
    {
        $url = parse_url($url, PHP_URL_PATH);
        if ($source['theme'] == 'madara') $ch_url = $url . '?style=list';

        if ($this->is_scid('chapter', $source)) {
            preg_match($source['scid']['chapter']['regex'], $url, $chapter_id);
            $data['chapter_id'] = $chapter_id[1];
        } else {
            $data['url'] = $url;
        }
        return $data;
    }

    public function run_parser($source_site)
    {
        $source = ucfirst(strtolower($source_site));
        $filePath = dirname(__DIR__) . '/Parsers/' . $source . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
            $className = 'Api\\Parsers\\' . $source . 'Parser';
            return $className;
        } else {
            // echo "File for source '$source' does not exist.";
            return false;
        }
    }

    // remove the old contents https://stackoverflow.com/a/38815450
    function set_inner_html($parent, $html)
    {
        $html = str_replace('&', '&amp;', $html);
        $fragment = $parent->ownerDocument->createDocumentFragment();
        $fragment->appendXML($html);
        while ($parent->hasChildNodes())
            $parent->removeChild($parent->firstChild);
        $parent->appendChild($fragment);
    }

    public function request($url, $options = [])
    {
        $response = Http::load($url, $options);
        // if (!$response->isSuccess() && $response->isBlocked()) $response = Http::bypass($url, $options);
        if (!$response->isSuccess() && $response->isBlocked()) $response = Http::proxy($url, $options);
        return $response;
    }

    private function request_fetch($link, $options, $parent)
    {
        $xml = $this->request($link, $options);

        if ($xml->isSuccess()) {
            $this->set_inner_html($parent, $xml->escape());
        }
    }

    public function latestPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) {
            $ls_exists = false;
            $source = xSelector::$source_site();
            $page = $this->param_check('page') ? $_GET['page'] : '1';
            $per_page = array_key_exists('per_page', $source) ? $source['per_page'] : 24;

            $parser = $this->run_parser($source_site);
            if (array_key_exists('parser', $source) && $source['parser'] == 'api' && $parser) {
                $locale = $source['lang'];

                if ($source_site == 'webtoons') {
                    $source_parser = new $parser($locale);
                    $source_link = str_replace('{$locale}', $locale, $source['url']['latest']);
                } else {
                    $source_parser = new $parser();
                    $source_link = str_replace('{$page}', $page, $source['url']['latest']);
                }
                // $source_link = $source_parser->response->link;

                $ls_data = $source_parser->getLatest('update', $page, $per_page);
                $ls_lists = $ls_data['lists'];
                $ls_exists = count($ls_lists) > 0;

                $next = $ls_data['next'];

                $source_xml = $source_parser->response;
                $prev = $page == '1' ? '' : (int)$page - 1;
                $prev = (string)$prev;
            } else {
                $options = [];
                if (array_key_exists('options', $source)) $options = array_merge($options, $source['options']);

                $source_link = str_replace('{$page}', $page, $source['url']['latest']);
                $source_xml = Http::load($source_link, $options);

                if ($source_xml->isEmpty()) {
                    $source_xml->status = 0;
                    return (object) $source_xml->showError("Empty Response ($source_site)");
                }

                $xpath = new DOMXpath($source_xml->responseParse());

                if ($source_xml->isBlocked($xpath)) {
                    // $source_xml = Http::bypass($source_link, $options);
                    $source_xml = Http::proxy($source_link, $options);
                    if ($source_xml->isSuccess()) {
                        if ($source_xml->isEmpty()) {
                            $source_xml->status = 0;
                            return (object) $source_xml->showError("Empty Response ($source_site)");
                        }

                        $xpath = new DOMXpath($source_xml->responseParse());
                    } else {
                        return (object) $source_xml->showError();
                    }
                } else {
                    if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                }

                if ($source_xml->isDomainChanged($xpath)) {
                    $source_xml->status = 0;
                    return (object) $source_xml->showError("Domain Changed ($source_site)");
                }

                if (array_key_exists('parser', $source) && is_array($source['parser']) && $parser) {
                    $source_parser = new $parser();
                    $ls_html = $source_parser->getLatest('update', $page, $per_page);
                    $this->set_inner_html($xpath->query($source['parser']['xpath'])->item(0), $ls_html->escape());
                }

                $lists = $xpath->query($source['LS']['parent']); //parent
                $ls_exists = $lists->length > 0;
                if ($ls_exists) {
                    $ls_lists = [];

                    foreach ($lists as $index) {
                        $type = '';
                        if (array_key_exists('type', $source['LS'])) {
                            $type_el = $this->queryX($xpath, $source['LS']['type']['xpath'], $index);
                            if ($type_el->length > 0) {
                                if ($this->keys_exist(['attr', 'regex'], $source['LS']['type'])) {
                                    $type_el = $type_el[0]->getAttribute($source['LS']['type']['attr']);
                                    $type_chk = preg_match($source['LS']['type']['regex'], $type_el, $type_match);
                                    $type = $type_chk ? strtolower($type_match[1]) : '';
                                } else {
                                    $type = strtolower($type_el[0]->textContent);
                                }
                            }
                        }

                        // NSFW
                        if ($source_site == 'maid' && preg_match('/\sdoujin/i', $type_el)) continue;

                        $color = '';
                        if (array_key_exists('color', $source['LS'])) {
                            $color_el = $this->queryX($xpath, $source['LS']['color']['xpath'], $index);
                            $color = $color_el->length > 0 ? true : false;
                        }

                        $completed = false;
                        if (array_key_exists('completed', $source['LS'])) {
                            $completed_el = $this->queryX($xpath, $source['LS']['completed']['xpath'], $index);
                            if ($completed_el->length > 0) {
                                if ($source['theme'] == 'madara' || array_key_exists('content', $source['LS']['completed'])) {
                                    $completed_str = trim($completed_el[0]->textContent);
                                } else {
                                    $completed_str = array_key_exists('attr', $source['LS']['completed'])
                                        ? $completed_el[0]->getAttribute($source['LS']['completed']['attr'])
                                        : trim($completed_el[0]->textContent);
                                }
                            } else {
                                $completed_str = '';
                            }
                            $completed = preg_match($source['LS']['completed']['regex'], $completed_str);
                            $completed = $completed === 0 ? false : true;
                        }

                        $date = '';
                        if (array_key_exists('date', $source['latest'])) {
                            $date_el = $this->queryX($xpath, $source['latest']['date']['xpath'], $index);
                            if ($date_el->length > 0) {
                                $date = array_key_exists('attr', $source['latest']['date'])
                                    ? $date_el[0]->getAttribute($source['latest']['date']['attr'])
                                    : trim($date_el[0]->textContent);
                            }
                        }

                        $title = trim($this->queryX($xpath, $source['LS']['title']['xpath'], $index)[0]->textContent);

                        $cover = '';
                        $cover_el = $this->queryX($xpath, $source['LS']['cover']['xpath'], $index);
                        if ($cover_el->length > 0) {
                            $attr_alt = array_key_exists('attr_alt', $source['LS']['cover']) && $cover_el[0]->hasAttribute($source['LS']['cover']['attr_alt']);
                            $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                            $cover = $cover_el[0]->getAttribute($source['LS']['cover'][$cover_attr]);
                            if ($this->is_missing_host($cover)) $cover = $this->get_host($source_link) . $cover;
                        }

                        $chapter = '';
                        $chapter_el = $this->queryX($xpath, $source['latest']['chapter']['xpath'], $index);
                        if ($chapter_el->length > 0) {
                            $chapter = preg_replace('/' . $this->rgx_vol . $this->rgx_ch . '/i', '', trim($chapter_el[0]->textContent));
                            $chapter = preg_replace('/' . preg_quote($title, '/') . '\s+/i', '', $chapter);
                            if (array_key_exists('regex', $source['latest']['chapter'])) $chapter = preg_replace($source['latest']['chapter']['regex'], '', $chapter);
                            $chapter = preg_replace(['/\n+/', '/\t+/', '/\s+/'], "\x20", $chapter);
                        }

                        $slug_url = $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['slug']['attr']);
                        preg_match($source['LS']['slug']['regex'], $slug_url, $slug_arr);
                        $slug = preg_replace($source['LS']['slug']['regex2'], '', $slug_arr[1]);

                        if ($source_site == 'mangapark') {
                            preg_match('/^([^\-]+)-/', $slug, $series_id);
                            $series_id = $series_id[1];
                            $slug = preg_replace('/[^\-]+\-' . $source['lang'] . '-/i', '', $slug);
                        }

                        if ($source_site == 'weebcentral') {
                            $slug_split = explode('/',  $slug_arr[1]);
                            $series_id = $slug_split[0];
                            $slug = strtolower(end($slug_split));
                        }

                        $list = [
                            'title' => $title,
                            'cover' => $cover,
                            'type' => $type,
                            'color' => $color,
                            'completed' => $completed,
                            'chapter' => trim($chapter),
                            'date' => $date,
                            'url' => $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['link']['attr']),
                            'slug' => $slug,
                        ];
                        if (in_array($source_site, ['mangapark', 'weebcentral'])) $list = array_merge(['series_id' => $series_id], $list);
                        array_push($ls_lists, $list);
                    }

                    if (array_key_exists('manual', $source['LS']['nav'])) {
                        $next_page = (int)$page + 1;

                        if ($source['theme'] == 'madara' || $source_site == 'komiku') {
                            $next_link = preg_replace('/([\?&\/]page[=\/])\d+/', '${1}' . $next_page, $source_link);
                            $next_xml = $this->request($next_link, $options);
                            $next = $next_xml->getStatus() == '404' ? '' : (string)$next_page;
                        }

                        if ($source['theme'] == 'tukutema') {
                            $next_btn = $this->queryX($xpath, $source['LS']['nav']['next']['xpath'], $lists);
                            if ($next_btn->length > 0) $next = (string)$next_page;
                        }

                        if ($source_site == 'weebcentral') {
                            $next_link = preg_replace('/\/\d+$/', '/' . $next_page, $source_link);
                            $next_xml = $this->request($next_link, $options);
                            $next = strlen($next_xml->response()) > 1 ? (string)$next_page : '';
                        }

                        if ($source_site == 'mangapark') {
                            $next_el = $xpath->query("//main//a[contains(@href, 'page=$next_page')]");
                            $next = $next_el->length > 0 ? (string)$next_page : '';
                        }

                        $prev = $page == '1' ? '' : (int)$page - 1;
                        $prev = (string)$prev;
                    } else {
                        $next = '';
                        $prev = '';

                        $n_pattern = $source['LS']['nav']['regex'];
                        $next_btn = $this->queryX($xpath, $source['LS']['nav']['next']['xpath'], $lists);
                        $prev_btn = $this->queryX($xpath, $source['LS']['nav']['prev']['xpath'], $lists);

                        // next button
                        if ($next_btn->length > 0) {
                            preg_match($n_pattern, $next_btn[0]->getAttribute($source['LS']['nav']['next']['attr']), $next);
                            $next = $next[1];
                        }

                        // prev button
                        if ($prev_btn->length > 0) {
                            $prev_index = $source['theme'] == 'madara' && $prev_btn->length > 1 ? ($prev_btn->length - 1) : 0;
                            preg_match($n_pattern, $prev_btn[$prev_index]->getAttribute($source['LS']['nav']['prev']['attr']), $prev);
                            $prev = empty($prev) ? '1' : $prev[1];
                        }
                    }
                }
            }

            if ($ls_exists) {
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
            } else {
                $data = [
                    'status' => 'NOT_FOUND',
                    'status_code' => 404,
                    'cache' => $source_xml->cache,
                    'bypass' => $source_xml->bypass,
                    'message' => 'Not Found',
                    'source' => $source_link,
                ];
            }

            return (object) $data;
        } else {
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        }
    }

    public function searchPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) {
            $data = [];
            $source = xSelector::$source_site();
            $querams = $this->param_check('params') ? 'params' : 'query';
            $value = isset($_GET[$querams]) && (!empty($_GET[$querams]) || $_GET[$querams] === '0') ?  $_GET[$querams] : null;

            if (!$value && $value !== '0') {
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            } else {
                $sc_exists = false;
                $page = $this->param_check('page') ? $_GET['page'] : '1';
                $per_page = array_key_exists('per_page', $source) ? $source['per_page'] : 24;
                $is_advanced = $this->param_check('params') ? true : false;
                $val = $is_advanced ? ($value == 'default' ? '' : $value) : rawurlencode($value);
                $val = str_replace([' ', '%20'], '+', $val);

                $parser = $this->run_parser($source_site);
                if (array_key_exists('parser', $source) && $source['parser'] == 'api' && $parser) {
                    $locale = $source['lang'];

                    if ($source_site == 'webtoons') {
                        $source_parser = new $parser($locale);

                        $search = ['{$locale}', '{$page}', '{$value}'];
                        $repl = [$locale, $page, $val];
                        $source_link = str_replace($search, $repl, $source['url']['search']);
                        // $source_link = $source_parser->response->link;
                    } else {
                        $source_parser = new $parser();

                        $search = ['{$page}', '{$value}'];
                        $full_url = $is_advanced ? ($value == 'default' && array_key_exists('default', $source['url']) ? 'default' : 'advanced') : 'search';
                        $source_link = str_replace($search, [$page, $val], $source['url'][$full_url]);
                        // $source_link = $source_parser->response->link;
                        if ($is_advanced && $value == 'default') $source_link = preg_replace('/[\?&]=?$/', '', $source_link); //removes last "?" or "&" character
                    }

                    if ($is_advanced && $value == 'default') {
                        $sc_data = $source_parser->getLatest('library', $page, $per_page);
                    } else {
                        $sc_data = $source_parser->getSearch($is_advanced, $val, $page, $per_page);
                    }

                    $sc_lists = $sc_data['lists'];
                    $sc_exists = count($sc_lists) > 0;

                    if ($source_site == 'softkomik') {
                        if ($is_advanced && strpos($val, 'sortBy=') === FALSE) $val = "sortBy=newKomik&$val";
                    }

                    $source_xml = $source_parser->response;
                    $next = $sc_data['next'];
                    $prev = $page == '1' ? '' : (int)$page - 1;
                    $prev = (string)$prev;
                } else {
                    if ($source_site == 'weebcentral') $page = $this->weebcentral_page('offset', $page, $per_page);

                    $search = ['{$page}', '{$value}'];
                    $full_url = $is_advanced ? ($value == 'default' && array_key_exists('default', $source['url']) ? 'default' : 'advanced') : 'search';
                    $source_link = str_replace($search, [$page, $val], $source['url'][$full_url]);

                    if ($is_advanced && $value == 'default') $source_link = preg_replace('/[\?&]=?$/', '', $source_link); //removes last "?" or "&" character
                    if ($source['theme'] == 'madara') {
                        if (strpos($source_link, '&s=') === FALSE) $source_link .= '&s';
                        if (strpos($source_link, '&type=') !== FALSE) $source_link = str_replace('&type', '&genre%5B%5D', $source_link);
                    }
                    if (strpos($value, 'order=') === FALSE && ($source['theme'] == 'koidezign' || $is_advanced && $value == 'default')) {
                        $s_qs = strpos($source_link, '?') !== FALSE ? '&' :'?';
                        $s_value = 'latest'; //order/sort by "added/created"
                        $s_url = 'order=' . $s_value;

                        if ($source['theme'] == 'madara') $s_url = 'm_orderby=new-manga';
                        if ($source['theme'] == 'tukutema') $s_url = 'orderby=bookmarked&order=desc';

                        if ($source_site == 'komikcast') $s_url = 'orderby=titleasc';
                        if ($source_site == 'komiku') $s_url = 'orderby=date';
                        if ($source_site == 'mangapark') $s_url = 'sortby=field_create';
                        if ($source_site == 'weebcentral') $s_url = 'sort=Recently+Added';

                        $source_link .= $s_qs . $s_url;
                    }

                    if (($source_site == 'weebcentral' && (!$is_advanced) || strpos($source_link, 'sort=Alphabet') !== FALSE)) $source_link = str_replace('&order=Descending', '', $source_link);

                    $options = [];
                    if (array_key_exists('options', $source)) $options = array_merge($options, $source['options']);

                    $source_xml = Http::load($source_link, $options);

                    if ($source_xml->isEmpty()) {
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Empty Response ($source_site)");
                    }

                    $xpath = new DOMXpath($source_xml->responseParse());

                    if ($source_xml->isBlocked($xpath)) {
                        // $source_xml = Http::bypass($source_link, $options);
                        $source_xml = Http::proxy($source_link, $options);
                        if ($source_xml->isSuccess()) {
                            if ($source_xml->isEmpty()) {
                                $source_xml->status = 0;
                                return (object) $source_xml->showError("Empty Response ($source_site)");
                            }

                            $xpath = new DOMXpath($source_xml->responseParse());
                        } else {
                            return (object) $source_xml->showError();
                        }
                    } else {
                        if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                    }

                    if ($source_xml->isDomainChanged($xpath)) {
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Domain Changed ($source_site)");
                    }

                    if (array_key_exists('parser', $source) && is_array($source['parser']) && $parser) {
                        $source_parser = new $parser();
                        if ($is_advanced && $value == 'default') {
                            $sc_html = $source_parser->getLatest('library', $page, $per_page);
                        } else {
                            $sc_html = $source_parser->getSearch($is_advanced, $val, $page, $per_page);
                        }
                        $this->set_inner_html($xpath->query($source['parser']['xpath'])->item(0), $sc_html->escape());
                    }

                    $lists = $xpath->query($this->search_path('parent', $source)); //parent
                    $sc_lists = [];
                    $sc_exists = $lists->length > 0;

                    if ($sc_exists) {
                        foreach ($lists as $index) {
                            $link_path = $this->search_path('link', $source);

                            $type = '';
                            $type_path = $this->search_path('type', $source);
                            if ($type_path) {
                                $type_el = $this->queryX($xpath, $type_path['xpath'], $index);
                                if ($type_el->length > 0) {
                                    if ($this->keys_exist(['attr', 'regex'], $type_path)) {
                                        $type_el = $type_el[0]->getAttribute($type_path['attr']);
                                        $type_chk = preg_match($type_path['regex'], $type_el, $type_match);
                                        $type = $type_chk ? strtolower($type_match[1]) : '';
                                    } else {
                                        $type = strtolower($type_el[0]->textContent);
                                    }
                                }
                            }

                            // NSFW
                            if ($source_site == 'maid' && preg_match('/\sdoujin/i', $type_el)) continue;

                            $color = '';
                            $color_path = $this->search_path('color', $source);
                            if ($color_path) {
                                $color_el = $this->queryX($xpath, $color_path['xpath'], $index);
                                $color = $color_el->length > 0 ? true : false;
                            }

                            $completed = false;
                            $completed_path = $this->search_path('completed', $source);
                            if ($completed_path) {
                                $completed_el = $this->queryX($xpath, $completed_path['xpath'], $index);
                                if ($completed_el->length > 0) {
                                    if ($source['theme'] == 'madara' || array_key_exists('content', $completed_path)) {
                                        $completed_str = trim($completed_el[0]->textContent);
                                    } else {
                                        $completed_str = array_key_exists('attr', $completed_path)
                                            ? $completed_el[0]->getAttribute($completed_path['attr'])
                                            : trim($completed_el[0]->textContent);
                                    }
                                } else {
                                    $completed_str = '';
                                }
                                $completed = preg_match($completed_path['regex'], $completed_str);
                                $completed = $completed === 0 ? false : true;
                            }

                            $title_path = $this->search_path('title', $source);
                            if ($source['theme'] == 'koidezign') {
                                $title = $this->queryX($xpath, $link_path['xpath'], $index)[0]->getAttribute($title_path['attr']);
                            } else {
                                $title = $this->queryX($xpath, $title_path['xpath'], $index)[0]->textContent;
                            }

                            $cover = '';
                            $cover_path = $this->search_path('cover', $source);
                            $cover_el = $this->queryX($xpath, $cover_path['xpath'], $index);
                            if ($cover_el->length > 0) {
                                $attr_alt = array_key_exists('attr_alt', $cover_path) && $cover_el[0]->hasAttribute($cover_path['attr_alt']);
                                $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                                $cover = $cover_el[0]->getAttribute($cover_path[$cover_attr]);
                                if ($this->is_missing_host($cover)) $cover = $this->get_host($source_link) . $cover;
                            }

                            $slug_path = $this->search_path('slug', $source);
                            $slug_url = $this->queryX($xpath, $link_path['xpath'], $index)[0]->getAttribute($slug_path['attr']);
                            preg_match($slug_path['regex'], $slug_url, $slug_arr);
                            $slug = preg_replace($slug_path['regex2'], '', $slug_arr[1]);

                            if ($source_site == 'mangapark') {
                                preg_match('/^([^\-]+)-/', $slug, $series_id);
                                $series_id = $series_id[1];
                                $slug = preg_replace('/[^\-]+\-' . $source['lang'] . '-/i', '', $slug);
                            }

                            if ($source_site == 'weebcentral') {
                                $slug_split = explode('/',  $slug_arr[1]);
                                $series_id = $slug_split[0];
                                $slug = strtolower(end($slug_split));
                            }

                            $list = [
                                'title' => trim($title),
                                'cover' => $cover,
                                'type' => $type,
                                'color' => $color,
                                'completed' => $completed,
                                'url' => $this->queryX($xpath, $link_path['xpath'], $index)[0]->getAttribute($link_path['attr']),
                                'slug' => $slug,
                            ];
                            if (in_array($source_site, ['mangapark', 'weebcentral'])) $list = array_merge(['series_id' => $series_id], $list);
                            array_push($sc_lists, $list);
                        }

                        $np_path = $this->search_path('nav', $source, $is_advanced);
                        if (array_key_exists('manual', $np_path)) {
                            if ($source_site == 'weebcentral') $page = $this->weebcentral_page('page', $page, $per_page);

                            $next_page = (int)$page + 1;
                            $next = '';

                            if ($source['theme'] == 'madara' || $source_site == 'komiku') {
                                $next_link = preg_replace('/([\?&\/]page[=\/])\d+/', '${1}' . $next_page, $source_link);
                                $next_xml = $this->request($next_link, $options);
                                $next = $next_xml->getStatus() == '404' ? '' : (string)$next_page;
                            }

                            if ($source['theme'] == 'tukutema') {
                                $next_btn = $this->queryX($xpath, $source['LS']['nav']['next']['xpath'], $lists);
                                if ($next_btn->length > 0) $next = (string)$next_page;
                            }

                            if ($source_site == 'weebcentral') {
                                $next_link = preg_replace('/([\?&]offset[=\/])\d+/', '${1}' . $this->weebcentral_page('offset', $next_page, $per_page), $source_link);
                                $next_xml = $this->request($next_link, $options);

                                if (!$next_xml->isEmpty()) {
                                    $next_xpath = new DOMXpath($next_xml->responseParse());
                                    $next = $next_xpath->query($this->search_path('parent', $source))->length > 0 ? (string)$next_page : '';
                                }
                            }

                            if ($source_site == 'mangapark') {
                                $next_el = $xpath->query("//main//a[contains(@href, 'page=$next_page')]");
                                $next = $next_el->length > 0 ? (string)$next_page : '';
                            }

                            $prev = $page == '1' ? '' : (int)$page - 1;
                            $prev = (string)$prev;
                        } else {
                            $next = '';
                            $prev = '';

                            $n_pattern = $np_path['regex'];
                            $next_btn = $this->queryX($xpath, $np_path['next']['xpath'], $lists);
                            $prev_btn = $this->queryX($xpath, $np_path['prev']['xpath'], $lists);

                            // next button
                            if ($next_btn->length > 0) {
                                preg_match($n_pattern, $next_btn[0]->getAttribute($np_path['next']['attr']), $next);
                                $next = $next[1];
                            }

                            // prev button
                            if ($prev_btn->length > 0) {
                                $prev_index = $source['theme'] == 'madara' && $prev_btn->length > 1 ? ($prev_btn->length - 1) : 0;
                                preg_match($n_pattern, $prev_btn[$prev_index]->getAttribute($np_path['prev']['attr']), $prev);
                                $prev = empty($prev) ? '1' : $prev[1];
                            }
                        }
                    } else {
                        // echo '{$lists} Not Found. $data = ';
                        $next = '';
                        $prev = '';
                    }
                }

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
            }

            return (object) $data;
        } else {
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        }
    }

    public function seriesPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) {
            $data = [];
            $source = xSelector::$source_site();
            $slug = $this->param_check('slug') ? $_GET['slug'] : null;
            $series_id = $this->param_check('seriesID') ? $_GET['seriesID'] : null;

            if (!$slug || ($this->is_scid('series', $source) && !$series_id)) {
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            } else {
                $sr_exists = false;

                $parser = $this->run_parser($source_site);
                if (array_key_exists('parser', $source) && $source['parser'] == 'api' && $parser) {
                    $locale = $source['lang'];

                    if ($source_site == 'webtoons') {
                        $source_parser = new $parser($locale);
                    } else {
                        $source_parser = new $parser();
                    }

                    $sr_param = $series_id ?? $slug;
                    $sr_data = $source_parser->getSeries($sr_param);
                    $source_xml = $source_parser->response;

                    $source_link = $sr_data['source'];
                    $sr_exists = !isset($sr_data['error']);

                    if ($sr_exists) {
                        $res_data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'slug' => $slug,
                        ];

                        if ($this->is_scid('series', $source)) $res_data['series_id'] = $series_id;

                        $data = array_merge($res_data, $sr_data);
                    }
                } else {
                    $options = [];
                    if (array_key_exists('options', $source)) $options = array_merge($options, $source['options']);

                    $repl = $this->is_scid('series', $source) ? $series_id : $slug;
                    $source_link = str_replace('{$slug}', $repl, $source['url']['series']);
                    $source_xml = Http::load($source_link, $options);

                    $xpath = new DOMXpath($source_xml->responseParse());

                    if ($source_xml->isBlocked($xpath)) {
                        // $source_xml = Http::bypass($source_link, $options);
                        $source_xml = Http::proxy($source_link, $options);
                        if ($source_xml->isSuccess()) {
                            $xpath = new DOMXpath($source_xml->responseParse());
                        } else {
                            return (object) $source_xml->showError();
                        }
                    } else {
                        if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                    }

                    if ($source_xml->isDomainChanged($xpath)) {
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Domain Changed ($source_site)");
                    }

                    $slink = '';
                    if (array_key_exists('shortlink', $source['series'])) {
                        $slink_el = $xpath->query($source['series']['shortlink']['xpath']);
                        if ($slink_el->length > 0) {
                            preg_match($source['series']['shortlink']['regex'], $slink_el[0]->getAttribute($source['series']['shortlink']['attr']), $shortlink);
                            $slink = empty($shortlink) ? '' : $shortlink[1];
                        } else {
                            $slink_el = $xpath->query("//article[contains(@id, 'post-')]");
                            if ($slink_el->length > 0) $slink = preg_replace('/post-(.*)/i', '', $slink_el[0]->getAttribute('id'));
                        }
                    }

                    $article = $xpath->query($source['series']['parent']); //parent
                    $sr_exists = $article->length > 0;
                    if ($sr_exists) {
                        $article = $article[0];

                        $detail = $source['series']['detail'];
                        $genres = $xpath->query($detail['genre']['xpath'], $article);
                        $gr_lists = [];

                        if ($genres->length > 0) {
                            foreach ($genres as $index) {
                                array_push($gr_lists, $index->textContent);
                            }
                        }

                        $title_str = $xpath->query($source['series']['title']['xpath'], $article)[0]->textContent;
                        $title = preg_replace($source['series']['title']['regex'], '', $title_str);
                        if (isset($source['series']['title']['regex2'])) $title = preg_replace($source['series']['title']['regex2'], '', $title);

                        $alternative = '';
                        if (array_key_exists('alternative', $source['series'])) {
                            $alt_el = $xpath->query($source['series']['alternative']['xpath'], $article);
                            $alternative = $alt_el->length > 0 ? trim($alt_el[0]->textContent) : '';
                            if (array_key_exists('regex', $source['series']['alternative'])) $alternative = preg_replace($source['series']['alternative']['regex'], '', $alternative);

                            if ($source_site == 'weebcentral') $alternative = preg_replace('/(\s+)?\n(\s+)?/', ', ', $alternative);
                        }

                        $cover = '';
                        $cover_el = $xpath->query($source['series']['cover']['xpath'], $article);
                        if ($cover_el->length > 0) {
                            $attr_alt = array_key_exists('attr_alt', $source['series']['cover']) && $cover_el[0]->hasAttribute($source['series']['cover']['attr_alt']);
                            $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                            $cover = $cover_el[0]->getAttribute($source['series']['cover'][$cover_attr]);
                            if ($this->is_missing_host($cover)) $cover = $this->get_host($source_link) . $cover;
                        }

                        $detail_list = [
                            'type' => strtolower($this->series_detail($xpath, 'type', $detail, $article)),
                            'status' => $this->series_detail($xpath, 'status', $detail, $article),
                            'released' => $this->series_detail($xpath, 'released', $detail, $article),
                            'author' => $this->series_detail($xpath, 'author', $detail, $article),
                            'artist' => $this->series_detail($xpath, 'artist', $detail, $article),
                            'genre' => implode(', ', $gr_lists),
                        ];

                        $desc = $xpath->query($source['series']['desc']['xpath'], $article);
                        if ($desc->length > 0) {
                            if (array_key_exists('remove', $source['series']['desc'])) {
                                foreach ($source['series']['desc']['remove'] as $remove_el) {
                                    $rm_lists = $xpath->query($remove_el, $desc[0]);
                                    foreach ($rm_lists as $el) {
                                        $el->parentNode->removeChild($el);
                                    }
                                }
                            }
                            $desc = $desc[0]->textContent;
                        } else {
                            $desc = '';
                        }

                        $sr_chapter = $source['series']['chapter'];

                        // pre-add chapter list
                        $ajax_check = isset($sr_chapter['fetch']) && $xpath->query($sr_chapter['fetch']['xpath'], $article)->length > 0;
                        if ($ajax_check) {
                            if ($source['theme'] == 'tukutema') {
                                preg_match('/postid-(\d+)/i', $xpath->query('//body')->item(0)->getAttribute('class'), $body_class);
                                $series_id = $body_class[1] ?? null;
                            }
                            $ch_link = array_key_exists('host', $sr_chapter['fetch']) ? $source['url']['host'] : $source_link;
                            if ($source['theme'] == 'tukutema' && $series_id && strpos($sr_chapter['fetch']['url'], '{$series_id}') !== false) {
                                $ch_link .= str_replace('{$series_id}', $series_id, $sr_chapter['fetch']['url']);
                            } else {
                                $ch_link .= $sr_chapter['fetch']['url'];
                            }
                            $ch_link = preg_replace('/(?<!:)\/{2}/', '/', $ch_link);
                            $ch_options = $source['theme'] == 'madara' ? ['method' => 'POST'] : [];
                            $ch_parent = $xpath->query($sr_chapter['fetch']['xpath'], $article)->item(0);
                            $this->request_fetch($ch_link, $ch_options, $ch_parent);
                        }

                        $chapters = $xpath->query($sr_chapter['xpath'], $article);
                        $ch_lists = [];

                        if ($chapters->length > 0) {
                            foreach ($chapters as $index) {
                                if ($source['theme'] == 'koidezign') {
                                    $date = $xpath->query("//*[contains(@class, 'date')]", $index)[0];
                                    $date->parentNode->removeChild($date);
                                }

                                $ch_url = $index->getAttribute($sr_chapter['attr']);
                                if ($source_xml::$proxy_host) $ch_url = str_replace($source_xml::$proxy_host, '', $ch_url);

                                $num_rgx = '/' . $this->rgx_vol . $this->rgx_ch . '/i';
                                if (isset($sr_chapter['num'])) {
                                    $ch_el = $xpath->query($sr_chapter['num']['xpath'], $index)[0];
                                    $ch_content = array_key_exists('attr', $sr_chapter['num']) ? $ch_el->getAttribute($sr_chapter['num']['attr']) : $ch_el->textContent;
                                } else {
                                    $ch_el = $index;
                                    $ch_content = $ch_el->textContent;
                                }
                                $ch_num = preg_replace($num_rgx, '', $ch_content); //remove "ch." etc.
                                $ch_num = preg_replace('/' . preg_quote($title, '/') . '\s+/i', '', $ch_num);
                                if (array_key_exists('regex2', $source['series']['title'])) $ch_num = preg_replace($source['series']['title']['regex2'], '', $ch_num);

                                $sr_slug = $slink ? preg_replace('/^' . $slink . '(\d+)?\-/i', '', $slug) : $slug; //remove shortlink
                                $sr_slug = preg_replace('/s\-/i', 's?-', $sr_slug); //eg. https://regexr.com/7es39
                                $ch_str = preg_replace('/' . $sr_slug . '/i', '', $ch_url);
                                $ch_str = preg_replace($sr_chapter['regex2'], '', $ch_str);
                                preg_match($sr_chapter['regex'], $ch_str, $chapter);

                                $ch_data = [
                                    'number' => $ch_num != '' ? trim($ch_num) : (count($chapter) > 0 ? $chapter[1] : ''),
                                ];
                                $ch_data = $this->chapter_source($source, $ch_url, $ch_data);

                                array_push($ch_lists, $ch_data);
                            }
                        }

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

                        // insert "series_id" into $data, before "title"
                        if ($this->is_scid('series', $source)) {
                            $position = 4;
                            $start = array_slice($data, 0, $position, true);
                            $end = array_slice($data, $position, null, true);
                            $data = $start + ['series_id' => $series_id] + $end;
                        }
                    }
                }

                if (!$sr_exists) {
                    $data = [
                        'status' => 'NOT_FOUND',
                        'status_code' => 404,
                        'cache' => $source_xml->cache,
                        'bypass' => $source_xml->bypass,
                        'message' => 'Not Found',
                        'source' => $source_link,
                    ];
                }
            }

            return (object) $data;
        } else {
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        }
    }

    public function chapterPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) {
            $data = [];
            $source = xSelector::$source_site();
            $slug = $this->param_check('slug') ? $_GET['slug'] : null;
            $chapter = isset($_GET['chapter']) && (!empty($_GET['chapter']) || $_GET['chapter'] === '0') ? $_GET['chapter'] : null;
            $url = $this->param_check('url') ? $_GET['url'] : null;
            $series_id = $this->param_check('seriesID') ? $_GET['seriesID'] : null;
            $chapter_id = $this->param_check('chapterID') ? $_GET['chapterID'] : null;

            if (!$slug || (!$chapter && $chapter !== '0') || ($this->is_scid('series', $source) && !$series_id) || ($this->is_scid('chapter', $source) && !$chapter_id)) {
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            } else {
                $ch_exists = false;

                $parser = $this->run_parser($source_site);
                if (array_key_exists('parser', $source) && $source['parser'] == 'api' && $parser) {
                    $locale = $source['lang'];

                    if ($source_site == 'webtoons') {
                        $source_parser = new $parser($locale);
                    } else {
                        $source_parser = new $parser();
                    }

                    $ch_params = ['slug' => $slug, 'chapter' => $chapter, 'url' => $url, 'series_id' => $series_id, 'chapter_id' => $chapter_id];
                    $ch_data = $source_parser->getChapter($ch_params);

                    $source_xml = $source_parser->response;
                    $source_link = $ch_data['source'];
                    $ch_exists = !isset($ch_data['error']);

                    if ($ch_exists) {
                        $res_data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'slug' => $slug,
                        ];

                        if ($this->is_scid('series', $source)) $res_data['series_id'] = $series_id;
                        if ($this->is_scid('chapter', $source)) $res_data['chapter_id'] = $chapter_id;

                        $data = array_merge($res_data, $ch_data);
                    }
                } else {
                    $options = [];
                    if (array_key_exists('options', $source)) $options = array_merge($options, $source['options']);

                    $search = ['{$slug}', '{$chapter}'];
                    $repl = [
                        ($this->is_scid('series', $source) ? $series_id : $slug),
                        ($this->is_scid('chapter', $source) ? $chapter_id : $chapter),
                    ];
                    $source_link = $url ? $source['url']['host'] . $url : str_replace($search, $repl, $source['url']['chapter']);
                    $source_xml = Http::load($source_link, $options);

                    $xpath = new DOMXpath($source_xml->responseParse());

                    if ($source_xml->isBlocked($xpath)) {
                        // $source_xml = Http::bypass($source_link, $options);
                        $source_xml = Http::proxy($source_link, $options);
                        if ($source_xml->isSuccess()) {
                            $xpath = new DOMXpath($source_xml->responseParse());
                        } else {
                            return (object) $source_xml->showError();
                        }
                    } else {
                        if (!$source_xml->isSuccess()) return (object) $source_xml->showError();
                    }

                    if ($source_xml->isDomainChanged($xpath)) {
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Domain Changed ($source_site)");
                    }

                    $content = $xpath->query($source['chapter']['parent']); //parent
                    $ch_exists = $content->length > 0;
                    if ($ch_exists) {
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

                        if (array_key_exists('json', $source['chapter'])) {
                            $cover = '';
                            $ch_script = $this->queryX($xpath, $source['chapter']['json']['xpath'], $content);

                            if ($ch_script->length > 0) {
                                if ($source['theme'] == 'themesia') {
                                    preg_match('/(\{[^;]+)\);?/', $ch_script[0]->textContent, $ts_reader);
                                    $ch_data = str_replace(['!0', '!1'], ['true', 'false'], $ts_reader[1]);
                                    $ch_data = json_decode($ch_data, true);

                                    $nav_rgx1 = $source['chapter']['json']['regex'];
                                    $nav_rgx2 = $source['chapter']['json']['regex2'];

                                    $next_url = $ch_data[$source['chapter']['json']['next']['name']];
                                    if ($next_url != '') {
                                        preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $next_url, $next_match);
                                        $next_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $next_match[0]);
                                        $next_str = str_replace($slug, '', $next_str);
                                        $next = [
                                            'number' => $next_str,
                                            'url' => parse_url($next_url, PHP_URL_PATH),
                                        ];
                                    } else {
                                        $next = json_decode('{}');
                                    }

                                    $prev_url = $ch_data[$source['chapter']['json']['prev']['name']];
                                    if ($prev_url != '') {
                                        preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $prev_url, $prev_match);
                                        $prev_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $prev_match[0]);
                                        $prev_str = str_replace($slug, '', $prev_str);
                                        $prev = [
                                            'number' => $prev_str,
                                            'url' => parse_url($prev_url, PHP_URL_PATH),
                                        ];
                                    } else {
                                        $prev = json_decode('{}');
                                    }

                                    $images = $ch_data['sources'][0]['images']; //"sources" & "images" from ts_reader

                                    if (count($images) > 0) {
                                        foreach ($images as $img) {
                                            array_push($img_lists, preg_replace('/\s/', '%20', $img));
                                        }
                                    }
                                }

                                if ($source_site == 'mangapark') {
                                    // https://github.com/KotatsuApp/kotatsu-parsers/blob/72564f5449990b1eb4f1a80745f445eecb177ca0/src/main/kotlin/org/koitharu/kotatsu/parsers/site/all/MangaPark.kt#L262

                                    function substringAfterLast($input, $delimiter) {
                                        $lastPos = strrpos($input, $delimiter);
                                        return $lastPos === false ? $input : substr($input, $lastPos + 1);
                                    }

                                    $s = $ch_script[0]->textContent;
                                    $script = strpos($s, '"comic-') !== false ? substringAfterLast($s, '"comic-') : substringAfterLast($s, '"manga-');

                                    preg_match_all('/"(https?:.+?)"/', $script, $matches);
                                    foreach ($matches[1] as $i => $img) {
                                        if (preg_match('/\.(jpg|jpeg|jfif|pjpeg|pjp|png|webp|avif|gif)$/i', $img)) array_push($img_lists, $img);
                                    }
                                }
                            } else {
                                echo '<b>' . $source['chapter']['json']['xpath'] . '</b> not found.';
                            }
                        } else {
                            if ($source_site == 'weebcentral') {
                                // pre-add image list
                                $img_link = $source_link . $source['chapter']['images']['fetch'];
                                $img_parent = $xpath->query($source['chapter']['images']['area'], $content)->item(0);
                                $this->request_fetch($img_link, [], $img_parent);

                                // pre-add nav links (next, prev)
                                $nav_link = str_replace(['{$slug}', '{$chapter}'], [$series_id, $chapter_id], $source['chapter']['nav']['fetch']);
                                $nav_parent = $xpath->query($source['chapter']['nav']['area'], $content)->item(0);
                                $this->request_fetch($nav_link, [], $nav_parent);
                            }

                            $cover = '';
                            if (array_key_exists('cover', $source['chapter'])) {
                                $cover_el = $this->queryX($xpath, $source['chapter']['cover']['xpath'], $content);
                                if ($cover_el->length > 0) {
                                    $attr_alt = array_key_exists('attr_alt', $source['chapter']['cover']) && $cover_el[0]->hasAttribute($source['chapter']['cover']['attr_alt']);
                                    $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                                    $cover = $cover_el[0]->getAttribute($source['chapter']['cover'][$cover_attr]);
                                    if ($this->is_missing_host($cover)) $cover = $this->get_host($source_link) . $cover;
                                }
                            }

                            $images = $this->queryX($xpath, $source['chapter']['images']['xpath'], $content);
                            if ($images->length > 0) {
                                foreach ($images as $img) {
                                    $attr_alt = array_key_exists('attr_alt', $source['chapter']['images']) && $img->hasAttribute($source['chapter']['images']['attr_alt']);
                                    $img_attr = $attr_alt ? 'attr_alt' : 'attr';
                                    $img_str = $img->getAttribute($source['chapter']['images'][$img_attr]);
                                    if ($source_site == 'reaper_scans') $img_str = base64_decode($img_str);
                                    if ($source_xml::$proxy_host) $img_str = str_replace($source_xml::$proxy_host, '', trim($img_str));
                                    array_push($img_lists, trim($img_str));
                                }
                            }
                        }

                        if (array_key_exists('nav', $source['chapter'])) {
                            $nav_rgx1 = $source['chapter']['nav']['regex'];
                            $nav_rgx2 = $source['chapter']['nav']['regex2'];
                            $num_rgx = '/' . $this->rgx_vol . $this->rgx_ch . '/i';

                            $next_btn = $this->queryX($xpath, $source['chapter']['nav']['next']['xpath'], $content);
                            if ($next_btn->length > 0) {
                                $next_url = $next_btn[0]->getAttribute($source['chapter']['nav']['next']['attr']);

                                if (array_key_exists('num', $source['chapter']['nav']['next'])) {
                                    $next_str = preg_replace($num_rgx, '', $next_btn[0]->textContent); //remove "ch." etc.
                                } else {
                                    preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $next_url, $next_match);
                                    $next_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $next_match[0]);
                                    $next_str = str_replace($slug, '', $next_str);
                                }

                                if ($source_site == 'reaper_scans') {
                                    preg_match('/location\.href=[\'"]([^\'"]+)[\'"]/', $next_url, $next_match);
                                    $next_url = $next_match[1];
                                }

                                if ($source_xml::$proxy_host) $next_url = str_replace($source_xml::$proxy_host, '', $next_url);

                                $next = [ 'number' => $next_str ];
                                $next = $this->chapter_source($source, $next_url, $next);
                            } else {
                                $next = json_decode('{}');
                            }

                            $prev_btn = $this->queryX($xpath, $source['chapter']['nav']['prev']['xpath'], $content);
                            if ($prev_btn->length > 0) {
                                $prev_url = $prev_btn[0]->getAttribute($source['chapter']['nav']['prev']['attr']);

                                if (array_key_exists('num', $source['chapter']['nav']['prev'])) {
                                    $prev_str = preg_replace($num_rgx, '', $prev_btn[0]->textContent); //remove "ch." etc.
                                } else {
                                    preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $prev_url, $prev_match);
                                    $prev_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $prev_match[0]);
                                    $prev_str = str_replace($slug, '', $prev_str);
                                }

                                if ($source_site == 'reaper_scans') {
                                    preg_match('/location\.href=[\'"]([^\'"]+)[\'"]/', $prev_url, $prev_match);
                                    $prev_url = $prev_match[1];
                                }

                                if ($source_xml::$proxy_host) $prev_url = str_replace($source_xml::$proxy_host, '', $prev_url);

                                $prev = [ 'number' => $prev_str ];
                                $prev = $this->chapter_source($source, $prev_url, $prev);
                            } else {
                                $prev = json_decode('{}');
                            }
                        }

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

                        // insert "series_id" or "chapter_id" into $data, before "title"
                        $chid_pos = array_key_exists('series_id', $data) ? 5 : 4;
                        if ($this->is_scid('series', $source)) $data = $this->add_scid(['series_id' => $series_id], $chid_pos, $data);
                        if ($this->is_scid('chapter', $source)) $data = $this->add_scid(['chapter_id' => $chapter_id], $chid_pos, $data);
                    }
                }

                if (!$ch_exists) {
                    $data = [
                        'status' => 'NOT_FOUND',
                        'status_code' => 404,
                        'cache' => $source_xml->cache,
                        'bypass' => $source_xml->bypass,
                        'message' => 'Not Found',
                        'source' => $source_link,
                    ];
                }
            }

            return (object) $data;
        } else {
            echo 'Source "' . $_GET['source'] . '" not listed in "$source_lists" (xSelector)';
            exit();
        }
    }
}
