<?php

namespace Api\Services;

class Route
{
    public function param_check($name, $arr)
    {
        return isset($arr[$name]) && (!empty($arr[$name]) || $arr[$name] != '');
    }

    // Delete files older than a given age https://gist.github.com/tdebatty/9412259
    private function delete($dir, $max_age) 
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
        
        if (isset($_SERVER['HTTP_ORIGIN'])) header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']); //plugin
        
        // https://stackoverflow.com/a/1678243/7598333
        if ($this->param_check('callback', $_GET)) :
            header('Content-Type: text/javascript; charset=utf8');

            echo $_GET['callback'] . '(\'api/' . $_GET['index'] . '\', ' . $new_data . ');';
        else :
            header('Content-Type: application/json; charset=utf8');
            echo $new_data;
        endif;
    }

    public function get($info = null, $callback, $target)
    {
        $day = 86400; //24 hours in seconds
        $path = __DIR__ . '/../../.cache/';
        $this->delete($path, $day); //delete cached file after 24 hours

        $super = $_GET;
        unset($super['url']);
        unset($super['site']);
        unset($super['cache']);

        $timer = $this->param_check('cache', $_GET) ? $_GET['cache'] : 30; //minutes
        $cachedTime = $timer * 60; //minutes to seconds
        $fileName = $path . implode('-', $super) . '.json';
        if (file_exists($fileName) && (filemtime($fileName) > (time() - $cachedTime)) && $super['index'] != 'search') :
            $file = file_get_contents($fileName, true);
            $file = json_decode($file);
        else :
            $controller = new $callback;
            $file = $controller->$target();

            if ($super['index'] != 'search' && $file->status_code == 200) :
                if (!is_dir($path)) mkdir($path, 0777, true);
                file_put_contents($fileName, json_encode($file));
            endif;
        endif;

        $this->show($file);
    }
}
