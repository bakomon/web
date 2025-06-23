<?php

namespace Api\Controllers;

require __DIR__ . '/../Services/Http.php';
require __DIR__ . '/../Services/xSelector.php';

use \DOMXpath;
use Api\Services\Http;
use Api\Services\xSelector;

class Controller
{
    private function param_check(string $name)
    {
        return isset($_GET[$name]) && (!empty($_GET[$name]) || $_GET[$name] != '');
    }

    private function mangasee_page($type, $op, $display)
    {
        if ($type == 'offset') return ($op - 1) * $display;
        if ($type == 'page') return intval($op / $display) + 1;
    }

    private function is_scid($page, $source)
    {
        if (array_key_exists('scid', $source)) {
            if (in_array($page, $source['scid'])) return true;
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

    public function run_parser($source_site)
    {
        $source = ucfirst(strtolower($source_site));
        $filePath = __DIR__ . '/../Parsers/' . $source . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
            $className = 'Api\\Parsers\\' . $source . 'Parser';
            return $className;
        } else {
            // echo "File for source '$source' does not exist.";
            return false;
        }
    }

    public function request($url, $options = [])
    {
        $response = Http::load($url, $options);
        // if (!$response->isSuccess() && $response->isBlocked()) $response = Http::bypass($url, $options);
        if (!$response->isSuccess() && $response->isBlocked()) $response = Http::proxy($url, $options);
        return $response;
    }

    private function request_fetch($link, $post, $dom, $parent)
    {
        $options = [];
        if ($post) $options['method'] = 'POST';
        $xml = $this->request($link, $options);

        if ($xml->isSuccess()) {
            $res = str_replace('&', '&amp;', $xml->response());
            $res = preg_replace('/\n?\s+[:@][\w\-\.]+="[^"]+"/', '', $res); //remove invalid attributes. e.g. ":class" or "@click"
            $res = preg_replace('/<input([^>]*?)(?<!\/)>/i', '<input$1 />', $res); //fix <input> tags not ending with />
            $data = $dom->createDocumentFragment();
            $data->appendXML($res);

            // remove the old contents https://stackoverflow.com/a/38815450
            while ($parent->hasChildNodes())
                $parent->removeChild($parent->firstChild);
            $parent->appendChild($data);
        }
    }

    public function latestPage()
    {
        $source_site = $this->param_check('source') ? $_GET['source'] : xSelector::$source_default;
        if (in_array($source_site, xSelector::$source_lists)) :
            $ls_exists = false;
            $source = xSelector::$source_site();
            $page = $this->param_check('page') ? $_GET['page'] : '1';

            if (array_key_exists('parser', $source) && $this->run_parser($source_site)) :
                $display = 24;
                $locale = $source['lang'];
                $parser = $this->run_parser($source_site);

                if ($source_site == 'webtoons') :
                    $source_parser = new $parser($locale);
                    $ls_data = $source_parser->getLatest('update');

                    $source_link = "https://webtoons.com/$locale/originals?sortOrder=UPDATE";
                    // $source_link = $source_parser->response->link;

                    if (!isset($ls_data['error'])) :
                        $chunks = array_chunk($ls_data, $display); //split array

                        $ls_lists = $chunks[(int)$page - 1] ?? [];
                        $ls_exists = count($ls_lists) > 0;

                        $next_page = (int)$page + 1;
                        $next = isset($chunks[$next_page]) ? (string)$next_page : '';
                    endif;
                else :
                    $source_parser = new $parser();
                endif;

                if (in_array($source_site, ['softkomik', 'comick'])) :
                    if ($source_site == 'comick') $display = 49; //49 comic per page (default)
                    $ls_data = $source_parser->getLatest('update', $page, $display);
                    $ls_lists = $ls_data['lists'];
                    $ls_exists = count($ls_lists) > 0;

                    $source_link = str_replace('{$page}', $page, $source['url']['latest']);
                    // $source_link = $source_parser->response->link;

                    $next = $ls_data['next'];
                endif;

                $source_xml = $source_parser->response;
                $prev = $page == '1' ? '' : (int)$page - 1;
                $prev = (string)$prev;
            else :
                $options = [];
                if (array_key_exists('headers', $source)) $options['headers'] = $source['headers'];

                $source_link = str_replace('{$page}', $page, $source['url']['latest']);
                $source_xml = Http::load($source_link, $options);

                if ($source_xml->isEmpty()) :
                    $source_xml->status = 0;
                    return (object) $source_xml->showError("Empty Response ($source_site)");
                endif;

                $xpath = new DOMXpath($source_xml->responseParse());

                if ($source_xml->isBlocked($xpath)) :
                    // $source_xml = Http::bypass($source_link, $options);
                    $source_xml = Http::proxy($source_link, $options);
                    if ($source_xml->isSuccess()) :
                        if ($source_xml->isEmpty()) :
                            $source_xml->status = 0;
                            return (object) $source_xml->showError("Empty Response ($source_site)");
                        endif;

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
                            if ($type_el->length > 0) :
                                if ($this->keys_exist(['attr', 'regex'], $source['LS']['type'])) :
                                    $type_el = $type_el[0]->getAttribute($source['LS']['type']['attr']);
                                    $type_chk = preg_match($source['LS']['type']['regex'], $type_el, $type_match);
                                    $type = $type_chk ? strtolower($type_match[1]) : '';
                                else :
                                    $type = strtolower($type_el[0]->textContent);
                                endif;
                            else :
                                $type = '';
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
                                if ($source['theme'] == 'madara' || array_key_exists('content', $source['LS']['completed'])) :
                                    $completed_str = trim($completed_el[0]->textContent);
                                else :
                                    $completed_str = $completed_el[0]->getAttribute($source['LS']['completed']['attr']);
                                endif;
                            else :
                                $completed_str = '';
                            endif;
                            $completed = preg_match($source['LS']['completed']['regex'], $completed_str);
                            $completed = $completed === 0 ? false : true;
                        else :
                            $completed = false;
                        endif;

                        if (array_key_exists('date', $source['latest'])) :
                            $date_el = $this->queryX($xpath, $source['latest']['date']['xpath'], $index);
                            if ($date_el->length > 0) :
                                if (array_key_exists('attr', $source['latest']['date'])) :
                                    $date_str = $date_el[0]->getAttribute($source['latest']['date']['attr']);
                                    $date = $date_str;
                                else :
                                    $date = trim($date_el[0]->textContent);
                                endif;
                            else :
                                $date = '';
                            endif;
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
                            $chapter = preg_replace('/c(?:[ha](?:ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(?:ap)?|ep)\b\.?\s+/i', '', trim($chapter[0]->textContent));
                            $chapter = preg_replace('/' . preg_quote($title, '/') . '\s+/i', '', $chapter);
                            if (array_key_exists('regex', $source['latest']['chapter'])) $chapter = preg_replace($source['latest']['chapter']['regex'], '', $chapter);
                            $chapter = preg_replace(['/\n+/', '/\t+/', '/\s+/'], "\x20", $chapter);
                        else :
                            $chapter = '';
                        endif;

                        $slug_url = $this->queryX($xpath, $source['LS']['link']['xpath'], $index)[0]->getAttribute($source['LS']['slug']['attr']);
                        preg_match($source['LS']['slug']['regex'], $slug_url, $slug_arr);
                        $slug = preg_replace($source['LS']['slug']['regex2'], '', $slug_arr[1]);

                        if ($source_site == 'mangapark') :
                            preg_match('/^([^\-]+)-/', $slug, $series_id);
                            $series_id = $series_id[1];
                            $slug = preg_replace('/[^\-]+\-' . $source['lang'] . '-/i', '', $slug);
                        endif;

                        if ($source_site == 'mangasee') :
                            $slug_split = explode('/',  $slug_arr[1]);
                            $series_id = $slug_split[0];
                            $slug = strtolower(end($slug_split));
                        endif;

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
                        if (in_array($source_site, ['mangapark', 'mangasee'])) $list = array_merge(['series_id' => $series_id], $list);
                        array_push($ls_lists, $list);
                    }

                    if (array_key_exists('manual', $source['LS']['nav'])) :
                        $next_page = (int)$page + 1;

                        if ($source['theme'] == 'madara' || $source_site == 'komiku') :
                            $next_link = preg_replace('/([\?&\/]page[=\/])\d+/', '${1}' . $next_page, $source_link);
                            $next_xml = $this->request($next_link, $options);
                            $next = $next_xml->getStatus() == '404' ? '' : (string)$next_page;
                        endif;

                        if ($source_site == 'mangasee') :
                            $next_link = preg_replace('/\/\d+$/', '/' . $next_page, $source_link);
                            $next_xml = $this->request($next_link, $options);
                            $next = strlen($next_xml->response()) > 1 ? (string)$next_page : '';
                        endif;

                        if ($source_site == 'mangapark') :
                            $next_el = $xpath->query("//main//a[contains(@href, 'page=$next_page')]");
                            $next = $next_el->length > 0 ? (string)$next_page : '';
                        endif;

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
            $source = xSelector::$source_site();
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
                $val = $is_advanced ? ($value == 'default' ? '' : $value) : rawurlencode($value);

                if (array_key_exists('parser', $source) && $this->run_parser($source_site)) :
                    $display = 24;
                    $locale = $source['lang'];
                    $parser = $this->run_parser($source_site);

                    if ($source_site == 'webtoons') :
                        $source_parser = new $parser($locale);

                        if ($is_advanced && $value == 'default') {
                            $sc_data = $source_parser->getLatest('added');
                            $source_link = "https://m.webtoons.com/$locale/new";
                            $chunks = array_chunk($sc_data, $display); //split array
                            $sc_lists = $chunks[(int)$page - 1] ?? [];
                        } else {
                            $sc_data = $source_parser->getSearch($value, $page);
                            $source_link = "https://webtoons.com/$locale/search?searchType=WEBTOON&page=$page&keyword=$value";
                            $sc_lists = $sc_data['lists'];
                            $chunks = array_chunk(range(1, $sc_data['total']), $sc_data['display']);
                        }
                        // $source_link = $source_parser->response->link;
                        $sc_exists = count($sc_lists) > 0;
                        $next_page = (int)$page + 1;
                        $next = isset($chunks[$next_page]) ? (string)$next_page : '';
                    else :
                        $source_parser = new $parser();
                    endif;

                    if (in_array($source_site, ['softkomik', 'comick'])) :
                        if ($source_site == 'comick') $display = 49; //49 comic per page (default)
                        if ($is_advanced && $value == 'default') {
                            $sc_data = $source_parser->getLatest('added', $page, $display);
                        } else {
                            $sc_data = $source_parser->getSearch($is_advanced, $value, $page, $display);
                        }

                        $sc_lists = $sc_data['lists'];
                        $sc_exists = count($sc_lists) > 0;

                        if ($source_site == 'softkomik') :
                            if ($is_advanced && $value == 'default') $val = 'sortBy=newKomik';
                            if ($is_advanced && strpos($val, 'sortBy=') === FALSE) $val = "sortBy=newKomik&$val";
                        endif;

                        $search = ['{$page}', '{$value}'];
                        $full_url = $is_advanced ? 'advanced' : 'search';
                        $source_link = str_replace($search, [$page, $val], $source['url'][$full_url]);
                        // $source_link = $source_parser->response->link;
                        if ($is_advanced && $value == 'default') $source_link = preg_replace('/[\?&]=?$/', '', $source_link);

                        $next = $sc_data['next'];
                    endif;

                    $source_xml = $source_parser->response;
                    $prev = $page == '1' ? '' : (int)$page - 1;
                    $prev = (string)$prev;
                else :
                    if ($source_site == 'mangasee') $page = $this->mangasee_page('offset', $page, 32); //32 series per page (default), cannot be changed

                    $search = ['{$page}', '{$value}'];
                    $full_url = $is_advanced ? 'advanced' : 'search';
                    $source_link = str_replace($search, [$page, $val], $source['url'][$full_url]);

                    if ($is_advanced && $value == 'default') $source_link = preg_replace('/[\?&]=?$/', '', $source_link);
                    if ($source['theme'] == 'madara') :
                        if (strpos($source_link, '&s=') === FALSE) $source_link .= '&s';
                        if (strpos($source_link, '&type=') !== FALSE) $source_link = str_replace('&type', '&genre%5B%5D', $source_link);
                    endif;
                    if (strpos($value, 'order=') === FALSE && ($source['theme'] == 'koidezign' || $is_advanced && $value == 'default')) :
                        $s_qs = strpos($source_link, '?') !== FALSE ? '&' :'?';
                        $s_value = 'latest'; //order/sort by "added/created"
                        $s_url = 'order=' . $s_value;

                        if ($source['theme'] == 'enduser') $s_url = 'orderby=update';
                        if ($source['theme'] == 'madara') $s_url = 'm_orderby=new-manga';
                        if ($source_site == 'komiku') $s_url = 'orderby=date';
                        if ($source_site == 'mangapark') $s_url = 'sortby=field_create';
                        if ($source_site == 'mangasee') $s_url = 'sort=Recently+Added';

                        $source_link .= $s_qs . $s_url;
                    endif;

                    if (($source_site == 'mangasee' && (!$is_advanced) || strpos($source_link, 'sort=Alphabet') !== FALSE)) $source_link = str_replace('&order=Descending', '', $source_link);

                    $options = [];
                    if (array_key_exists('headers', $source)) $options['headers'] = $source['headers'];

                    $source_xml = Http::load($source_link, $options);

                    if ($source_xml->isEmpty()) :
                        $source_xml->status = 0;
                        return (object) $source_xml->showError("Empty Response ($source_site)");
                    endif;

                    $xpath = new DOMXpath($source_xml->responseParse());

                    if ($source_xml->isBlocked($xpath)) :
                        // $source_xml = Http::bypass($source_link, $options);
                        $source_xml = Http::proxy($source_link, $options);
                        if ($source_xml->isSuccess()) :
                            if ($source_xml->isEmpty()) :
                                $source_xml->status = 0;
                                return (object) $source_xml->showError("Empty Response ($source_site)");
                            endif;

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

                    $lists = $xpath->query($this->search_path('parent', $source)); //parent
                    $sc_lists = [];
                    $sc_exists = $lists->length > 0;

                    if ($sc_exists) :
                        foreach ($lists as $index) {
                            $link_path = $this->search_path('link', $source);

                            $type_path = $this->search_path('type', $source);
                            if ($type_path) :
                                $type_el = $this->queryX($xpath, $type_path['xpath'], $index);
                                if ($type_el->length > 0) :
                                    if ($this->keys_exist(['attr', 'regex'], $type_path)) :
                                        $type_el = $type_el[0]->getAttribute($type_path['attr']);
                                        $type_chk = preg_match($type_path['regex'], $type_el, $type_match);
                                        $type = $type_chk ? strtolower($type_match[1]) : '';
                                    else :
                                        $type = strtolower($type_el[0]->textContent);
                                    endif;
                                else :
                                    $type = '';
                                endif;
                            else :
                                $type = '';
                            endif;

                            $color_path = $this->search_path('color', $source);
                            if ($color_path) :
                                $color = $this->queryX($xpath, $color_path['xpath'], $index);
                                $color = $color->length > 0 ? true : false;
                            else :
                                $color = '';
                            endif;

                            $completed_path = $this->search_path('completed', $source);
                            if ($completed_path) :
                                $completed_el = $this->queryX($xpath, $completed_path['xpath'], $index);
                                if ($completed_el->length > 0) :
                                    if ($source['theme'] == 'madara' || array_key_exists('content', $completed_path)) :
                                        $completed_str = trim($completed_el[0]->textContent);
                                    else :
                                        $completed_str = $completed_el[0]->getAttribute($completed_path['attr']);
                                    endif;
                                else :
                                    $completed_str = '';
                                endif;
                                $completed = preg_match($completed_path['regex'], $completed_str);
                                $completed = $completed === 0 ? false : true;
                            else :
                                $completed = false;
                            endif;

                            $title_path = $this->search_path('title', $source);
                            if ($source['theme'] == 'koidezign') :
                                $title = $this->queryX($xpath, $link_path['xpath'], $index)[0]->getAttribute($title_path['attr']);
                            else :
                                $title = $this->queryX($xpath, $title_path['xpath'], $index)[0]->textContent;
                            endif;

                            $cover_path = $this->search_path('cover', $source);
                            $cover = $this->queryX($xpath, $cover_path['xpath'], $index);
                            if ($cover->length > 0) :
                                $attr_alt = array_key_exists('attr_alt', $cover_path) && $cover[0]->hasAttribute($cover_path['attr_alt']);
                                $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                                $cover = $cover[0]->getAttribute($cover_path[$cover_attr]);
                            else :
                                $cover = '';
                            endif;

                            // NSFW
                            if ($source_site == 'maid' && preg_match('/\sdoujin/i', $type_el)) continue;

                            $slug_path = $this->search_path('slug', $source);
                            $slug_url = $this->queryX($xpath, $link_path['xpath'], $index)[0]->getAttribute($slug_path['attr']);
                            preg_match($slug_path['regex'], $slug_url, $slug_arr);
                            $slug = preg_replace($slug_path['regex2'], '', $slug_arr[1]);

                            if ($source_site == 'mangapark') :
                                preg_match('/^([^\-]+)-/', $slug, $series_id);
                                $series_id = $series_id[1];
                                $slug = preg_replace('/[^\-]+\-' . $source['lang'] . '-/i', '', $slug);
                            endif;

                            if ($source_site == 'mangasee') :
                                $slug_split = explode('/',  $slug_arr[1]);
                                $series_id = $slug_split[0];
                                $slug = strtolower(end($slug_split));
                            endif;

                            $list = [
                                'title' => trim($title),
                                'cover' => $cover,
                                'type' => $type,
                                'color' => $color,
                                'completed' => $completed,
                                'url' => $this->queryX($xpath, $link_path['xpath'], $index)[0]->getAttribute($link_path['attr']),
                                'slug' => $slug,
                            ];
                            if (in_array($source_site, ['mangapark', 'mangasee'])) $list = array_merge(['series_id' => $series_id], $list);
                            array_push($sc_lists, $list);
                        }

                        $np_path = $this->search_path('nav', $source, $is_advanced);
                        if (array_key_exists('manual', $np_path)) :
                            if ($source_site == 'mangasee') $page = $this->mangasee_page('page', $page, 32); //32 series per page (default), cannot be changed

                            $next_page = (int)$page + 1;

                            if ($source['theme'] == 'madara' || $source_site == 'komiku') :
                                $next_link = preg_replace('/([\?&\/]page[=\/])\d+/', '${1}' . $next_page, $source_link);
                                $next_xml = $this->request($next_link, $options);
                                $next = $next_xml->getStatus() == '404' ? '' : (string)$next_page;
                            endif;

                            if ($source_site == 'mangasee') :
                                $next_link = preg_replace('/([\?&]offset[=\/])\d+/', '${1}' . $this->mangasee_page('offset', $next_page, 32), $source_link);
                                $next_xml = $this->request($next_link, $options);

                                if ($next_xml->isEmpty()) :
                                    $next = '';
                                else :
                                    $next_xpath = new DOMXpath($next_xml->responseParse());
                                    $next = $next_xpath->query("//div[@class='serieslist']")->length > 0 ? (string)$next_page : '';
                                endif;
                            endif;

                            if ($source_site == 'mangapark') :
                                $next_el = $xpath->query("//main//a[contains(@href, 'page=$next_page')]");
                                $next = $next_el->length > 0 ? (string)$next_page : '';
                            endif;

                            $prev = $page == '1' ? '' : (int)$page - 1;
                            $prev = (string)$prev;
                        else :
                            $n_pattern = $np_path['regex'];
                            $next_btn = $this->queryX($xpath, $np_path['next']['xpath'], $lists);
                            $prev_btn = $this->queryX($xpath, $np_path['prev']['xpath'], $lists);

                            // next button
                            if ($next_btn->length > 0) :
                                preg_match($n_pattern, $next_btn[0]->getAttribute($np_path['next']['attr']), $next);
                                $next = $next[1];
                            else :
                                $next = '';
                            endif;

                            // prev button
                            if ($prev_btn->length > 0) :
                                $prev_index = $source['theme'] == 'madara' && $prev_btn->length > 1 ? ($prev_btn->length - 1) : 0;
                                preg_match($n_pattern, $prev_btn[$prev_index]->getAttribute($np_path['prev']['attr']), $prev);
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
            $source = xSelector::$source_site();
            $slug = $this->param_check('slug') ? $_GET['slug'] : null;
            $series_id = $this->param_check('seriesID') ? $_GET['seriesID'] : null;

            if (!$slug || ($this->is_scid('series', $source) && !$series_id)) :
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            else :
                $sr_exists = false;

                if (array_key_exists('parser', $source) && $this->run_parser($source_site)) :
                    $locale = $source['lang'];
                    $parser = $this->run_parser($source_site);

                    if ($source_site == 'webtoons') :
                        $source_parser = new $parser($locale);
                        $sr_param = $series_id;
                    else :
                        $source_parser = new $parser();
                        $sr_param = $slug;
                    endif;

                    $sr_data = $source_parser->getSeries($sr_param);
                    $source_xml = $source_parser->response;

                    $source_link = $sr_data['source'];
                    $sr_exists = !isset($sr_data['error']);

                    if ($sr_exists) :
                        $res_data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'slug' => $slug,
                        ];
                        if ($source_site == 'webtoons') $res_data['seriesID'] = $series_id;

                        $data = array_merge($res_data, $sr_data);
                    endif;
                else :
                    $options = [];
                    if (array_key_exists('headers', $source)) $options['headers'] = $source['headers'];

                    $repl = $this->is_scid('series', $source) ? $series_id : $slug;
                    $source_link = str_replace('{$slug}', $repl, $source['url']['series']);
                    $source_xml = Http::load($source_link, $options);

                    $dom = $source_xml->responseParse();
                    $xpath = new DOMXpath($dom);

                    if ($source_xml->isBlocked($xpath)) :
                        // $source_xml = Http::bypass($source_link, $options);
                        $source_xml = Http::proxy($source_link, $options);
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
                            $slink = empty($shortlink) ? '' : $shortlink[1];
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

                        if (array_key_exists('alternative', $source['series'])) {
                            $alternative = $xpath->query($source['series']['alternative']['xpath'], $article);
                            $alternative = $alternative->length > 0 ? trim($alternative[0]->textContent) : '';
                            if (array_key_exists('regex', $source['series']['alternative'])) $alternative = preg_replace($source['series']['alternative']['regex'], '', $alternative);
                            if ($source_site == 'mangasee') $alternative = preg_replace('/(\s+)?\n(\s+)?/', ', ', $alternative);
                        } else {
                            $alternative = '';
                        }

                        $cover = $xpath->query($source['series']['cover']['xpath'], $article);
                        if ($cover->length > 0) :
                            $attr_alt = array_key_exists('attr_alt', $source['series']['cover']) && $cover[0]->hasAttribute($source['series']['cover']['attr_alt']);
                            $cover_attr = $attr_alt ? 'attr_alt' : 'attr';
                            $cover = $cover[0]->getAttribute($source['series']['cover'][$cover_attr]);
                        else :
                            $cover = '';
                        endif;

                        if (array_key_exists('type', $detail)) {
                            $type_el = $xpath->query($detail['type']['xpath'], $article);
                            $type = $type_el->length > 0 ? $type_el[0]->textContent : '';
                            if (array_key_exists('regex', $detail['type'])) $type = preg_replace($detail['type']['regex'], '', $type);
                        } else {
                            $type = '';
                        }

                        if (array_key_exists('status', $detail)) {
                            $status_el = $xpath->query($detail['status']['xpath'], $article);
                            $status = $status_el->length > 0 ? $status_el[0]->textContent : '';
                            if (array_key_exists('regex', $detail['status'])) $status = preg_replace($detail['status']['regex'], '', $status);
                        } else {
                            $status = '';
                        }

                        if (array_key_exists('released', $detail)) {
                            $released_el = $xpath->query($detail['released']['xpath'], $article);
                            $released = $released_el->length > 0 ? $released_el[0]->textContent : '';
                            if (array_key_exists('regex', $detail['released'])) $released = preg_replace($detail['released']['regex'], '', $released);
                        } else {
                            $released = '';
                        }

                        if (array_key_exists('author', $detail)) {
                            $author_el = $xpath->query($detail['author']['xpath'], $article);
                            $author = $author_el->length > 0 ? $author_el[0]->textContent : '';
                            if (array_key_exists('regex', $detail['author'])) $author = preg_replace($detail['author']['regex'], '', $author);
                        } else {
                            $author = '';
                        }

                        if (array_key_exists('artist', $detail)) {
                            $artist_el = $xpath->query($detail['artist']['xpath'], $article);
                            $artist = $artist_el->length > 0 ? $artist_el[0]->textContent : '';
                            if (array_key_exists('regex', $detail['artist'])) $artist = preg_replace($detail['artist']['regex'], '', $artist);
                        } else {
                            $artist = '';
                        }

                        $detail_list = [
                            'type' => strtolower(trim($type)),
                            'status' => trim($status),
                            'released' => trim($released),
                            'author' => trim($author),
                            'artist' => trim($artist),
                            'genre' => implode(', ', $gr_lists),
                        ];

                        $desc = $xpath->query($source['series']['desc']['xpath'], $article);
                        $desc = $desc->length > 0 ? $desc[0]->textContent : '';

                        // pre-add chapter list
                        $ajax_check = array_key_exists('fetch', $source['series']['chapter']) && $xpath->query($source['series']['chapter']['area'], $article)->length > 0;
                        if ($ajax_check) :
                            $ch_link = $source_link . $source['series']['chapter']['fetch'];
                            $ch_method = $source['theme'] == 'madara' ? true : false;
                            $ch_parent = $xpath->query($source['series']['chapter']['area'], $article)[0];
                            $this->request_fetch($ch_link, $ch_method, $dom, $ch_parent);
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
                                if ($source_xml::$proxy) :
                                    $ch_url = parse_url($ch_url, PHP_URL_PATH);
                                    $ch_url = preg_replace('/^\//', '', $ch_url);
                                endif;

                                $num_rgx = '/((c([ha](ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(ap)?|ep)\b)(\s+)?[\.\:]?(\s+)?|vol(ume)?[\.\:\s]+\d+)/i';
                                $ch_el = array_key_exists('num', $source['series']['chapter']) ? $xpath->query($source['series']['chapter']['num'], $index)[0] : $index;
                                $ch_num = preg_replace($num_rgx, '', $ch_el->textContent); //remove "ch." etc.
                                $ch_num = preg_replace('/' . preg_quote($title, '/') . '\s+/i', '', $ch_num);
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

                        // insert "seriesID" into $data, before "title"
                        if ($this->is_scid('series', $source)) :
                            $position = 4;
                            $start = array_slice($data, 0, $position, true);
                            $end = array_slice($data, $position, null, true);
                            $data = $start + ['seriesID' => $series_id] + $end;
                        endif;
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
            $source = xSelector::$source_site();
            $slug = $this->param_check('slug') ? $_GET['slug'] : null;
            $chapter = isset($_GET['chapter']) && (!empty($_GET['chapter']) || $_GET['chapter'] === '0') ? $_GET['chapter'] : null;
            $series_id = $this->param_check('seriesID') ? $_GET['seriesID'] : null;
            $chapter_id = $this->param_check('chapterID') ? $_GET['chapterID'] : null;

            if (!$slug || (!$chapter && $chapter !== '0') || ($this->is_scid('series', $source) && !$series_id) || ($this->is_scid('chapter', $source) && !$chapter_id)) :
                $data = [
                    'status' => 'BAD_REQUEST',
                    'status_code' => 400,
                    'message' => 'Bad Request',
                ];
            else :
                $ch_exists = false;

                if (array_key_exists('parser', $source) && $this->run_parser($source_site)) :
                    $locale = $source['lang'];
                    $parser = $this->run_parser($source_site);

                    if ($source_site == 'webtoons') :
                        $source_parser = new $parser($locale);
                        $series_id = $this->param_check('seriesID') ? $_GET['seriesID'] : null;
                        $ch_param = $series_id;
                    else :
                        $source_parser = new $parser();
                        $ch_param = $slug;
                    endif;

                    $ch_no = $this->is_scid('chapter', $source) ? $chapter_id : $chapter;
                    $ch_data = $source_parser->getChapter($ch_param, $ch_no);
                    $source_xml = $source_parser->response;

                    $source_link = $ch_data['source'];
                    $ch_exists = !isset($ch_data['error']);

                    if ($ch_exists) :
                        $res_data = [
                            'status' => 'SUCCESS',
                            'status_code' => $source_xml->getStatus(),
                            'cache' => $source_xml->cache,
                            'bypass' => $source_xml->bypass,
                            'slug' => $slug,
                        ];

                        if ($this->is_scid('series', $source)) $res_data['seriesID'] = $series_id;
                        if ($this->is_scid('chapter', $source)) $res_data['chapterID'] = $chapter_id;
                        if ($source_site == 'webtoons') $res_data['title'] = '';

                        $data = array_merge($res_data, $ch_data);
                    endif;
                else :
                    $url = $this->param_check('url') ? $_GET['url'] : null;

                    $options = [];
                    if (array_key_exists('headers', $source)) $options['headers'] = $source['headers'];

                    $search = ['{$slug}', '{$chapter}'];
                    $repl = [
                        ($this->is_scid('series', $source) ? $series_id : $slug),
                        ($this->is_scid('chapter', $source) ? $chapter_id : $chapter),
                    ];
                    $source_link = $url ? $source['url']['host'] . $url : str_replace($search, $repl, $source['url']['chapter']);
                    $source_xml = Http::load($source_link, $options);

                    $dom = $source_xml->responseParse();
                    $xpath = new DOMXpath($dom);

                    if ($source_xml->isBlocked($xpath)) :
                        // $source_xml = Http::bypass($source_link, $options);
                        $source_xml = Http::proxy($source_link, $options);
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

                        if (array_key_exists('json', $source['chapter'])) :
                            $cover = '';
                            $ch_script = $this->queryX($xpath, $source['chapter']['json']['xpath'], $content);

                            if ($ch_script->length > 0) :
                                if ($source['theme'] == 'themesia') :
                                    preg_match('/(\{[^;]+)\);?/', $ch_script[0]->textContent, $ts_reader);
                                    $ch_data = str_replace(['!0', '!1'], ['true', 'false'], $ts_reader[1]);
                                    $ch_data = json_decode($ch_data, true);

                                    $nav_rgx1 = $source['chapter']['json']['regex'];
                                    $nav_rgx2 = $source['chapter']['json']['regex2'];

                                    $next_url = $ch_data[$source['chapter']['json']['next']['name']];
                                    if ($next_url != '') :
                                        preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $next_url, $next_match);
                                        $next_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $next_match[0]);
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
                                        preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $prev_url, $prev_match);
                                        $prev_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $prev_match[0]);
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
                                endif;

                                if ($source_site == 'mangapark') :
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
                                endif;
                            else :
                                echo '<b>' . $source['chapter']['json']['xpath'] . '</b> not found.';
                            endif;
                        else :
                            if ($source_site == 'mangasee') :
                                // pre-add image list
                                $img_link = $source_link . $source['chapter']['images']['fetch'];
                                $img_parent = $xpath->query($source['chapter']['images']['area'], $content)[0];
                                $this->request_fetch($img_link, false, $dom, $img_parent);

                                // pre-add nav links (next, prev)
                                $nav_link = str_replace(['{$slug}', '{$chapter}'], [$series_id, $chapter_id], $source['chapter']['nav']['fetch']);
                                $nav_parent = $xpath->query($source['chapter']['nav']['area'], $content)[0];
                                $this->request_fetch($nav_link, false, $dom, $nav_parent);
                            endif;

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

                            $images = $this->queryX($xpath, $source['chapter']['images']['xpath'], $content);
                            if ($images->length > 0) :
                                foreach ($images as $img) {
                                    $attr_alt = array_key_exists('attr_alt', $source['chapter']['images']) && $img->hasAttribute($source['chapter']['images']['attr_alt']);
                                    $img_attr = $attr_alt ? 'attr_alt' : 'attr';
                                    $img_str = $img->getAttribute($source['chapter']['images'][$img_attr]);
                                    if ($source_site == 'reaper_scans') $img_str = base64_decode($img_str);
                                    array_push($img_lists, trim($img_str));
                                }
                            endif;
                        endif;

                        if (array_key_exists('nav', $source['chapter'])) :
                            $nav_rgx1 = $source['chapter']['nav']['regex'];
                            $nav_rgx2 = $source['chapter']['nav']['regex2'];
                            $num_rgx = '/((c([ha](ap|ao|hp|pa|a|p))[tp]er|episode|\b(ch(ap)?|ep)\b)(\s+)?[\.\:]?(\s+)?|vol(ume)?[\.\:]\d+)/i';

                            $next_btn = $this->queryX($xpath, $source['chapter']['nav']['next']['xpath'], $content);
                            if ($next_btn->length > 0) :
                                $next_url = $next_btn[0]->getAttribute($source['chapter']['nav']['next']['attr']);

                                if (array_key_exists('num', $source['chapter']['nav']['next'])) :
                                    $next_str = preg_replace($num_rgx, '', $next_btn[0]->textContent); //remove "ch." etc.
                                else :
                                    preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $next_url, $next_match);
                                    $next_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $next_match[0]);
                                    $next_str = str_replace($slug, '', $next_str);
                                endif;

                                if ($source_site == 'reaper_scans') :
                                    preg_match('/location\.href=[\'"]([^\'"]+)[\'"]/', $next_url, $next_match);
                                    $next_url = $next_match[1];
                                endif;

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

                                if (array_key_exists('num', $source['chapter']['nav']['prev'])) :
                                    $prev_str = preg_replace($num_rgx, '', $prev_btn[0]->textContent); //remove "ch." etc.
                                else :
                                    preg_match('/' . $nav_rgx1 . $nav_rgx2 . '/i', $prev_url, $prev_match);
                                    $prev_str = preg_replace('/' . $nav_rgx1 . '[\-\/]/i', '', $prev_match[0]);
                                    $prev_str = str_replace($slug, '', $prev_str);
                                endif;

                                if ($source_site == 'reaper_scans') :
                                    preg_match('/location\.href=[\'"]([^\'"]+)[\'"]/', $prev_url, $prev_match);
                                    $prev_url = $prev_match[1];
                                endif;

                                $prev = [
                                    'number' => $prev_str,
                                    'url' => parse_url($prev_url, PHP_URL_PATH) . ($source['theme'] == 'madara' ? '?style=list' : ''),
                                ];
                            else :
                                $prev = json_decode('{}');
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

                        // insert "seriesID" or "chapterID" into $data, before "title"
                        $chid_pos = array_key_exists('seriesID', $data) ? 5 : 4;
                        if ($this->is_scid('series', $source)) $data = $this->add_scid(['seriesID' => $series_id], 4, $data);
                        if ($this->is_scid('chapter', $source)) $data = $this->add_scid(['chapterID' => $chapter_id], $chid_pos, $data);
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
