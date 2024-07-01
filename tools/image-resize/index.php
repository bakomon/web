<?php
require 'vendor/autoload.php';
require '../../api/Allowed.php';

use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;
use \Bramus\Router\Router;
use \Api\Allowed;

$router = new Router();

if ((new Allowed)->check()) header('Access-Control-Allow-Origin: *');

/* Get Method */
$router->get('/', function() {
  $image_url = isset($_GET['imageUrl']) ? $_GET['imageUrl'] : null;
  $width = isset($_GET['width']) ? $_GET['width'] : null;
  $height = isset($_GET['height']) ? $_GET['height'] : null;
  $quality = isset($_GET['quality']) ? $_GET['quality'] : 85;
  
  //Check the input for GET.
  if (!$image_url) {
    show_error('Please provide the url of image.');
    return;
  }

  if (!$width && !$height) {
    show_error('Please input width or height which you want to resize to.');
    return;
  }

  if ($width && !is_numeric($width) || $height && !is_numeric($height) || $quality && !is_numeric($quality)) {
    show_error('Width, Height, and Quality should be number.');
    return;
  }

  //Check file size and type, if everything is OK, download it.
  if (!check_file_ok($image_url)) {
    show_error('Input file should be an image, and the size should not larger than 5MB.');
    return;
  }

  $folder_path = './.temp-files/';
  if (!is_dir($folder_path)) mkdir($folder_path, 0777, true);

  $image_info = pathinfo(parse_url($image_url, PHP_URL_PATH));
  $image_path = $folder_path . $image_info['basename'];
  file_put_contents($image_path, fopen($image_url, 'r'));

  try {
    $file_path = $image_path;
    $size = getimagesize($image_url);
    if ($width && $size[0] > (int) $width || $height && $size[1] > (int) $height) {
      $image = new ImageResize($image_path);
      if($width && $height){
        $image->resizeToBestFit((int) $width, (int) $height, $allow_enlarge = TRUE);
      } else {
        if($width) $image->resizeToWidth((int) $width, $allow_enlarge = TRUE);
        if($height) $image->resizeToHeight((int) $height, $allow_enlarge = TRUE);
      }
      $image->save($image_path);

      if ($image_info['extension'] != 'webp') {
        $resmush = resmushit($image_path, (int) $quality);
        if (isset($resmush['error'])) {
          show_error('reSmush: ' . $resmush['error_long']);
        } else {
          show_success($resmush['dest'], pathinfo($resmush['dest'], PATHINFO_BASENAME));
        }
      } else {
        $imgbb = imgbb($image_path, 1800); //expiration = 1800 (30 minutes)
        if (isset($imgbb['success'])) {
          show_success($imgbb['data']['display_url'], $image_info['basename']);
        } else {
          show_error('ImgBB: ' . $imgbb['message']);
        }
      }
    } else {
      show_success($image_url, $image_info['basename']);
    }

    unlink($file_path);

  } catch (ImageResizeException $e) {
    unlink($file_path);
    show_error($e->getMessage());
  }
});


/* Post method */
$router->post('/', function() {
  $post_data = file_get_contents('php://input');
  $image_data = json_decode($post_data, true);

  //Check image url is existed.
  if (!isset($image_data['imageUrl'])) {
    show_error('Please provide the url of image.');
    return;
  }

  $image_url = $image_data['imageUrl'];

  //Check file size and type, if everything is OK, download it.
  if (!check_file_ok($image_url)) {
    show_error('Input file should be an image, and the size should not larger than 5MB.');
    return;
  }

  $folder_path = './.temp_files/';
  if (!is_dir($folder_path)) mkdir($folder_path, 0777, true);

  $image_info = pathinfo(parse_url($image_url, PHP_URL_PATH));
  $image_path = $folder_path . $image_info['basename'];
  file_put_contents($image_path, fopen($image_url, 'r'));

  //Resize image to fit size.
  try {
    $width = isset($image_data['width']) ? $image_data['width'] : null;
    $height = isset($image_data['height']) ? $image_data['height'] : null;
    $quality = isset($image_data['quality']) ? $image_data['quality'] : 85;

    if (!$width && !$height) {
      show_error('Please input width or height which you want to resize to.');
      return;
    }
    if ($width && !is_numeric($width) || $height && !is_numeric($height) || $quality && !is_numeric($quality)) {
      unlink($image_path);
      show_error('Width, Height, and Quality should be number.');
      return;
    }

    $file_path = $image_path;
    $size = getimagesize($image_url);
    if ($width && $size[0] > (int) $width || $height && $size[1] > (int) $height) {
      $image = new ImageResize($image_path);
      if($width && $height){
        $image->resizeToBestFit((int) $width, (int) $height, $allow_enlarge = TRUE);
      } else {
        if($width) $image->resizeToWidth((int) $width, $allow_enlarge = TRUE);
        if($height) $image->resizeToHeight((int) $height, $allow_enlarge = TRUE);
      }
      $image->save($image_path);

      if ($image_info['extension'] != 'webp') {
        $resmush = resmushit($image_path, (int) $quality);
        if (isset($resmush['error'])) {
          show_error('reSmush: ' . $resmush['error_long']);
        } else {
          show_success($resmush['dest'], pathinfo($resmush['dest'], PATHINFO_BASENAME));
        }
      } else {
        $imgbb = imgbb($image_path, 1800); //expiration = 1800 (30 minutes)
        if (isset($imgbb['success'])) {
          show_success($imgbb['data']['display_url'], $image_info['basename']);
        } else {
          show_error('ImgBB: ' . $imgbb['message']);
        }
      }
    } else {
      show_success($image_url, $image_info['basename']);
    }

    unlink($file_path);

  } catch (ImageResizeException $e) {
    unlink($file_path);
    show_error($e->getMessage());
  }
});

$router->run();

/**
* Function for show success data in JSON format.
*
* @param string $data The success data from ImgBB
*/
function show_success($url, $name) {
  $result = [
    'status' => 'success',
    'filename' => $name,
    'image_url' => $url,
  ];

  header('Content-Type: application/json');
  echo json_encode($result);
}

/**
* Function for show error mesage in JSON format.
*
* @param string $message The error message which should be displayed.
*/
function show_error($message) {
  $result = [
    'status' => 'failed',
    'error_message' => $message,
  ];
  header('Content-Type: application/json');
  echo json_encode($result);
}

/**
* Function for check received file before download it.
*
* @param string $url The url of image.
*/
function check_file_ok($url) {
  $headers = array_change_key_case(get_headers($url, true), CASE_LOWER);

  //if response code not 200, return RESPONSE CODE
  if (substr(array_values($headers)[0], 9, 3) != '200') {
    show_error(array_values($headers)[0]);
    exit;
  }

  $file_size = $headers['content-length'];
  $file_type = $headers['content-type'];

  //If the file more then 5MB, return FALSE
  if (!$file_size || $file_size > 5000000) {
    return FALSE;
  }
  //If not image, return FALSE
  if (!preg_match('/image\/(png|jpe?g|gif|webp)/', $file_type)) {
    return FALSE;
  }
  return TRUE;
}

/**
* Function for image optimization with reSmush.it API
* 
* Source: https://resmush.it/api
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
* Source: https://api.imgbb.com/
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

/**
* Function for image upload (temporary) with ThumbSnap API
* 
* Source: https://thumbsnap.com/api
*/
function thumbsnap($file) {
  $API_KEY = 'YOUR_THUMBSNAP_APIKEY';
  $mime = mime_content_type($file);
  $name = pathinfo($file, PATHINFO_BASENAME);
  $media = new CURLFile($file, $mime, $name);
  $data = array(
    'key' => $API_KEY,
    'media' => $media,
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://thumbsnap.com/api/upload');
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $result = curl_exec($ch);
  if (curl_errno($ch)) {
     $result = curl_error($ch);
  }
  curl_close ($ch);
  return $result;
  // return json_decode($result, true);
}
