<?php

namespace Api\Services;

class Route
{
    public function param_check($name, $arr)
    {
        return isset($arr[$name]) && (!empty($arr[$name]) || $arr[$name] != '');
    }

    // Delete files older than a given age https://gist.github.com/tdebatty/9412259
    private function delete_older_than($dir, $max_age)
    {
        $list = array();
        $limit = time() - $max_age;
        $dir = realpath($dir);

        if (!is_dir($dir)) return;

        $dh = opendir($dir);
        if ($dh === false) return;

        while (($file = readdir($dh)) !== false) {
          $file = $dir . '/' . $file;
          if (!is_file($file)) continue;

          if (filemtime($file) < $limit) {
            $list[] = $file;
            unlink($file);
          }

        }
        closedir($dh);
    }

    public function show($data)
    {
        http_response_code($data->status_code);
        $new_data = json_encode($data);

        // https://stackoverflow.com/a/1678243
        if ($this->param_check('callback', $_GET)) :
            header('Content-Type: text/javascript; charset=utf8');
            echo $_GET['callback'] . '(\'api/' . $_GET['index'] . '\', ' . $new_data . ');';
        else :
            header('Content-Type: application/json; charset=utf8');
            echo $new_data;
        endif;
    }

    public function get($info, $callback, $target)
    {
        $day = 86400; //24 hours in seconds
        $path = sys_get_temp_dir() . '/.cache/';
        $this->delete_older_than($path, $day); //delete all cached file after 24 hours

        $timer = $this->param_check('cache', $_GET) ? $_GET['cache'] : 30; //minutes
        $cachedTime = $timer * 60; //minutes to seconds

        hQuery::$cache_path = $path;
        hQuery::$cache_expires = $cachedTime;

        $controller = new $callback;
        $this->show($controller->$target());
    }
}
