<?php

require_once '../api/Allowed.php';

use \Api\Allowed;

// Prevent direct url access
if (!(new Allowed)->check(['only_referer' => true])) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit;
}

// #===========================================================================================#

/**
 * Function to checks if a specified key exists in an array and its corresponding value is not empty.
 *
 * @param string $name The key to check in the array.
 * @param array $arr The array to check for the existence of the key.
 */
function param_check($name, $arr) {
  return isset($arr[$name]) && (!empty($arr[$name]) || $arr[$name] != '');
}

/**
 * Converts a quality value from a range of 0-100 to a range of 9-0.
 *
 * @param int $quality The input quality value, must be between 0 and 100 inclusive.
 * @return int The converted quality value within the range of 9 to 0.
 * @throws InvalidArgumentException If the input quality value is not within the valid range.
 */
function convertQuality($quality) {
  // Check if the input is within the valid range
  if ($quality < 0 || $quality > 100) {
    throw new InvalidArgumentException("Quality value must be between 0 and 100.");
  }

  // Convert the quality from 0-100 range to 9-0 range and round the result
  $convertedQuality = round(9 - (0.09 * $quality));

  return $convertedQuality;
}

/**
 * Function to get formatted headers (with response code).
 *
 * @param array $headers The php headers to be parsed
 * @link https://www.php.net/manual/en/reserved.variables.httpresponseheader.php#117203
 */
function parse_header($headers) {
  $head = array();
  foreach( $headers as $k=>$v ) {
    $t = explode( ':', $v, 2 );
    if ( isset($t[1]) ) {
      $head[ strtolower(trim($t[0])) ] = trim( $t[1] );
    } else {
      $head[] = $v;
      if ( preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out) ) $head['reponse_code'] = intval( $out[1] );
    }
  }
  return $head;
}

/**
 * Function to load an image with custom headers.
 *
 * @param string $url The URL of the image.
 * @param array $data An associative array containing data related to the image.
 */
function image_get($url, $ref = null) {
  $context = null;
  if ($ref) {
    if (substr($ref, -1) !== '/') $ref .= '/';
    $options = [
      'http' => [
          'header' => 'Referer: ' . $ref,
      ],
    ];
    $context = stream_context_create($options);
  }
  $response = @file_get_contents($url, false, $context);
  $headers = $http_response_header;
  $data = [
    "headers" => parse_header($headers),
    "response" => $response,
  ];
  if ($response !== false) $data['size'] = getimagesizefromstring($response);
  return (object) $data;
}

/**
 * Function that uses modification time (mtime) to delete files older than a given age.
 *
 * @param string $dir The directory path where files are located.
 * @param int $max_age The maximum age (in seconds) for files to be considered old and deleted.
 *
 * @link https://gist.github.com/tdebatty/9412259
 */
function delete_older_than($dir, $max_age) {
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

/**
 * Function to check if a file exists and if its modification time exceeds a specified threshold.
 *
 * @param string $name The name of the file containing cached data.
 * @param int $time The time threshold (in seconds).
 */
function cache_file($name, $time) {
  if (file_exists($name) && filemtime($name) > (time() - $time)) {
    $file = file_get_contents($name, true);
    return json_decode($file, true);
  } else {
    return FALSE;
  }
}

/**
 * Function for show success data in JSON format.
 *
 * @param string $data The success data which should be displayed.
 */
function show_success($data, $url, $name, $blkd = false) {
  $result = [
    'status' => 'success',
    'filename' => $name,
    'img_url' => $url,
    // 'img_base64' => 'data:' . $data->headers['content-type'] . ';base64,' . base64_encode($data->response),
  ];
  if ($blkd) $result['blocked'] = true;

  header('Content-Type: application/json');
  echo json_encode($result);
}

/**
 * Function for show error mesage in JSON format.
 *
 * @param string $message The error message which should be displayed.
 */
function show_error($message, $url = null) {
  $result = [
    'status' => 'failed',
    'error_message' => $message,
  ];
  if ($url) $result['img_url'] = $url;
  header('Content-Type: application/json');
  echo json_encode($result);
}

/**
 * Function for check received file before download it.
 *
 * @param string $url The url of image.
 * @param array $headers List of response headers.
 */
function check_file_ok($data) {
  $headers = $data->headers;

  // If response code not 200, return RESPONSE CODE
  if ($headers['reponse_code'] != '200') {
    show_error(array_values($headers)[0]);
    exit;
  }

  $file_size = isset($headers['content-length']) ? $headers['content-length'] : -1;
  $file_type = isset($data->size) && $data->size !== false ? $data->size['mime'] : $headers['content-type'];

  // If the file more than 5MB, return FALSE
  if (!$file_size || $file_size > 5000000) {
    return FALSE;
  }
  // If not image, return FALSE
  if (!preg_match('/image\/(png|jpe?g|gif|webp)/', $file_type)) {
    return FALSE;
  }
  return TRUE;
}

/**
 * Function for image optimization with reSmush.it API
 *
 * @link https://resmush.it/api
 */
function resmushit($file, $quality = 92) {
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $output = new CURLFile($file, $mime, $name);
  $data = array(
      'files' => $output,
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty=' . $quality);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $result = curl_exec($ch);
  if (curl_errno($ch)) {
     $result = curl_error($ch);
  }
  curl_close ($ch);
  return json_decode($result, true);
}

/**
 * Function for image upload (temporary) with ImgBB API
 *
 * @link https://api.imgbb.com/
 */
function imgbb($file, $expiration) {
  $API_KEY = 'YOUR_IMGBB_APIKEY';
  $image = base64_encode(file_get_contents($file));
  $data = array(
    'image' => $image,
    'expiration' => $expiration,
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload?key=' . $API_KEY);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $result = curl_exec($ch);
  if (curl_errno($ch)) {
     $result = curl_error($ch);
  }
  curl_close ($ch);
  return json_decode($result, true);
}

// #===========================================================================================#

$img_blocked = false;
$img_url = param_check('imageUrl', $_GET) ? $_GET['imageUrl'] : null;
$img_ref = param_check('ref', $_GET) ? $_GET['ref'] : null;
$width = param_check('width', $_GET) ? $_GET['width'] : null;
$height = param_check('height', $_GET) ? $_GET['height'] : null;
$quality = param_check('quality', $_GET) ? $_GET['quality'] : 85;

// Check the input for GET.
if (!$img_url) {
  show_error('Please provide the url of image.');
  return;
}

if ($width && !is_numeric($width) || $height && !is_numeric($height) || $quality && !is_numeric($quality)) {
  show_error('Width, Height, and Quality should be number.');
  return;
}

$img_res = image_get($img_url);
if ($img_res->response === false) {
  $img_blocked = true;
  $img_res = image_get($img_url, $img_ref);
}

// Check file size and type, if everything is OK, download it.
if (!check_file_ok($img_res)) {
  show_error('Input file should be an image, and the size should not larger than 5MB.');
  return;
}

$folder_path = './.temp-files/';
if (!is_dir($folder_path)) mkdir($folder_path, 0777, true);

$img_parse = parse_url($img_url);
$img_info = pathinfo($img_parse['path']);
$img_path = $folder_path . $img_info['basename'];
file_put_contents($img_path, $img_res->response);

try {
  $file_path = $img_path;
  $size = $img_res->size;

  if ($width && $size[0] > (int)$width || $height && $size[1] > (int)$height) {
    $wsrv_size = '';
    if ($width && $height){
      $wsrv_size = "w=$width&h=$height";
    } else {
      if ($width) $wsrv_size = "w=$width";
      if ($height) $wsrv_size = "h=$height";
    }

    // wsrv.nl (old)
    // if ($img_info['extension'] == 'png') {
    //   $quality = convertQuality($quality);
    //   $quality_str = "l=$quality";
    // } else {
    //   $quality_str = "q=$quality";
    // }

    if ($img_blocked) {
      delete_older_than($folder_path, 86400); //24 hours
      $cache_name = param_check('name', $_GET) ? $_GET['name'] : $img_info['basename'];
      $cache_path = $folder_path . $img_info['extension'] . '-' . $cache_name. '.json';

      if ($img_info['extension'] != 'webp') {
        $expiration = 300; //5 minutes
        $cache = cache_file($cache_path, $expiration);

        $qlty = 100; //only used as image storage
        $resmush = $cache ? $cache : resmushit($img_path, $qlty); //5 minutes (default)

        if (isset($resmush['error'])) {
          show_error('reSmush: ' . $resmush['error_long'], $img_url);
        } else {
          file_put_contents($cache_path, json_encode($resmush));
          $res_url = "https://YOUR_VERCEL_PROJECT.vercel.app/imgpa?$wsrv_size&url=" . rawurlencode($resmush['dest']) . "&q=$quality";
          if (isset($img_parse['fragment'])) $res_url .= '#' . $img_parse['fragment'];
          show_success($img_res, $res_url, $img_info['basename']);
        }
      } else {
        $expiration = 1800; //30 minutes
        $cache = cache_file($cache_path, $expiration);
        $imgbb = $cache ? $cache : imgbb($img_path, $expiration);

        if (isset($imgbb['success'])) {
          file_put_contents($cache_path, json_encode($imgbb));
          $res_url = "https://YOUR_VERCEL_PROJECT.vercel.app/imgpa?$wsrv_size&url=" . rawurlencode($imgbb['data']['display_url']) . "&q=$quality";
          if (isset($img_parse['fragment'])) $res_url .= '#' . $img_parse['fragment'];
          show_success($img_res, $res_url, $img_info['basename']);
        } else {
          show_error('ImgBB: ' . $imgbb['message'], $img_url);
        }
      }
    } else {
      $res_url = "https://YOUR_VERCEL_PROJECT.vercel.app/imgpa?$wsrv_size&url=" . rawurlencode($img_url) . "&q=$quality";
      show_success($img_res, $res_url, $img_info['basename']);
    }
  } else {
    show_success($img_res, $img_url, $img_info['basename'], $img_blocked);
  }
  unlink($file_path);

} catch (ImageResizeException $e) {
  unlink($file_path);
  show_error($e->getMessage());
}
