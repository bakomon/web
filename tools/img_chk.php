<?php

// Image Processing API

require_once '../api/Allowed.php';
require_once './curl.php';

use \Api\Allowed;
use Tools\cURL;

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
 * Note: user-agent and website address as referer is mandatory.
 *
 * @link https://resmush.it/api
 */
function resmushit($file, $referer, $quality = 92) {
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'files' => new CURLFile($file, $mime, $name),
  ];

  $result = cURL::post('http://api.resmush.it/?qlty=' . $quality, [
    'fields' => $data,
    'useragent' => 'MyCustomUserAgent/1.0',
    'referer' => $referer ?? 'https://example.com',
  ]);

  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with ImgBB API (chevereto)
 *
 * @link https://api.imgbb.com/
 */
function imgbb($file, $expiration = 1800) {
  $API_KEY = 'YOUR_IMGBB_APIKEY';
  $data = [
    'image' => base64_encode(file_get_contents($file)),
    'expiration' => $expiration,
  ];

  $result = cURL::post('https://api.imgbb.com/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with upanh API (chevereto)
 *
 * @link https://upanh.org/api-v1
 */
function upanh($file, $expiration = 1800) {
  $API_KEY = 'a6fb90181564047e9633dc18cd36a736'; //public API key
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'source' => new CURLFile($file, $mime, $name),
    'expiration' => $expiration,
  ];

  $result = cURL::post('https://upanh.org/api/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with FreeImgHost API (chevereto)
 *
 * @link https://freeimghost.net/api-v1
 */
function freeimghost($file, $expiration = 1800) {
  $API_KEY = '0df1c83381bcc0ba88e3ec928d09e494'; //public API key
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'source' => new CURLFile($file, $mime, $name),
    'expiration' => $expiration,
  ];

  $result = cURL::post('https://freeimghost.net/api/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with AnhMoe API (chevereto)
 *
 * @link https://anh.moe/page/apidoc
 */
function anhmoe($file, $expiration = 1800) {
  $API_KEY = 'anh.moe_public_api'; //public API key
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'source' => new CURLFile($file, $mime, $name),
    'expiration' => $expiration,
  ];

  $result = cURL::post('https://anh.moe/api/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with ImgCDN API (chevereto)
 * Note: guests images will expire in 2 weeks.
 *
 * @link https://imgcdn.dev/page/api
 */
function imgcdn($file) {
  $API_KEY = '5386e05a3562c7a8f984e73401540836'; //public API key
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'source' => new CURLFile($file, $mime, $name),
  ];

  $result = cURL::post('https://imgcdn.dev/api/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with Tinypic API (chevereto)
 * Note: guests images will expire in 1 week.
 *
 * @link https://tinypic.host/api-v1
 */
function tinypic($file) {
  $API_KEY = 'fbf7790f8eaf570a3723efcccedac8d6bcdd7e12cb3b5e005c584814728afcfd'; //public API key
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'source' => new CURLFile($file, $mime, $name),
  ];

  $result = cURL::post('https://tinypic.host/api/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload (temporary) with PicHost API (chevereto)
 * Note: guests images will expire in 6 months.
 *
 * @link https://pichost.net/api-v1
 */
function pichost($file) {
  $API_KEY = '10f3dcb13fd98f4c86ab139259a4bd5f0737180dec534ac1e3332e432a6c15ee'; //public API key
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $data = [
    'source' => new CURLFile($file, $mime, $name),
  ];

  $result = cURL::post('https://pichost.net/api/1/upload?key=' . $API_KEY, ['fields' => $data]);
  return json_decode($result::$source, true);
}

/**
 * Function for image upload with Postimages API
 *
 * @link https://github.com/Inirit/Reuploader/blob/835ccad2ab8252833ecebbda0ed97596bbb12097/ts/handlers/PostImage.ts
 * @link https://postimages.org/app
 * @link https://postimage.org/settings.php?api=1
 * @link https://postimages.org/login/api
 */
function postimages($file, $apikey = null) {
  $API_KEY = '8ca0b57a6bb9c4c33cd9e7ab8e6a7f05'; //public API key
  $unique_id = [
    'fb733cccce28e7db3ff9f17d7ccff3d1',
    '59c2ad4b46b0c1e12d5703302bff0120',
  ];
  $name = pathinfo($file, PATHINFO_FILENAME);
  $type = pathinfo($file, PATHINFO_EXTENSION);

  shuffle($unique_id);
  $data = [
    'key' => $apikey ?? $API_KEY,
    // 'gallery' => 'bakomon', //optional
    'o' => '2b819584285c102318568238c7d4a4c7',
    'm' => $unique_id[0], //unique device identifier
    'version' => '1.0.1',
    'portable' => '1', //optional
    'name' => $name,
    'type' => $type, //file extension
    'image' => base64_encode(file_get_contents($file)),
  ];

  $headers = [
    'User-Agent: Mozilla/5.0 (compatible; Postimage/1.0.1; +http://postimage.org/app.php)',
  ];

  $result = cURL::post('https://api.postimage.org/1/upload', [
    'fields' => $data,
    'headers' => $headers,
  ]);

  $parsed_xml = simplexml_load_string($result::$source);
  $output = json_encode($parsed_xml);

  return json_decode($output, true);
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

$folder_path = sys_get_temp_dir() . '/.temp-files/';
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
    $IMGPA_URL = 'https://YOUR_VERCEL_PROJECT.vercel.app/imgpa';

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
      // If the image is blocked, temporarily upload it to Resmush or <chevereto>
      delete_older_than($folder_path, 86400); //24 hours
      $cache_name = param_check('name', $_GET) ? $_GET['name'] : $img_info['basename'];
      $cache_path = $folder_path . $img_info['extension'] . '-' . $cache_name. '.json';

      if ($img_info['extension'] != 'webp') {
        $expiration = 300; //5 minutes
        $cache = cache_file($cache_path, $expiration);

        $qlty = 100;
        $resmush = $cache ? $cache : resmushit($img_path, $img_ref, $qlty); //5 minutes (default)

        if (isset($resmush['error'])) {
          show_error('reSmush: ' . $resmush['error_long'], $img_url);
        } else {
          file_put_contents($cache_path, json_encode($resmush));
          $res_url = "$IMGPA_URL?$wsrv_size&url=" . rawurlencode($resmush['dest']) . "&q=$quality&hide_error";
          if (isset($img_parse['fragment'])) $res_url .= '#' . $img_parse['fragment'];
          show_success($img_res, $res_url, $img_info['basename']);
        }
      } else {
        $expiration = 1800; //30 minutes
        $cache = cache_file($cache_path, $expiration);

        $imgbb = $cache ? $cache : imgbb($img_path, $expiration);
        if (isset($imgbb['success'])) {
          file_put_contents($cache_path, json_encode($imgbb));
          $res_url = "$IMGPA_URL?$wsrv_size&url=" . rawurlencode($imgbb['data']['display_url']) . "&q=$quality&hide_error";
          if (isset($img_parse['fragment'])) $res_url .= '#' . $img_parse['fragment'];
          show_success($img_res, $res_url, $img_info['basename']);
        } else {
          show_error('ImgBB: ' . $imgbb['message'], $img_url);
        }

        // $postimages = $cache ? $cache : postimages($img_path);
        // if (array_key_exists('error', $postimages)) {
        //   show_error('Postimages: ' . $postimages['error'], $img_url);
        // } else {
        //   file_put_contents($cache_path, json_encode($postimages));
        //   $postimages_url = $postimages['links']['hotlink'] . '#' . parse_url($postimages['links']['delete'], PHP_URL_PATH);
        //   $res_url = "$IMGPA_URL?$wsrv_size&url=" . rawurlencode($postimages_url) . "&q=$quality&hide_error";
        //   if (isset($img_parse['fragment'])) $res_url .= '#' . $img_parse['fragment'];
        //   show_success($img_res, $res_url, $img_info['basename']);
        // }

      }
    } else {
      $res_url = "$IMGPA_URL?$wsrv_size&url=" . rawurlencode($img_url) . "&q=$quality&hide_error";
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
