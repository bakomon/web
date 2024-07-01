<?php
// Source: https://github.com/front/firebase-backup/blob/master/php/index.php

require_once '../api/Allowed.php';

use \Api\Allowed;

// Prevent direct url access
if (!(new Allowed)->check()) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit;
}

function param_check($name, $arr)
{
  return isset($arr[$name]) && (!empty($arr[$name]) || $arr[$name] != '');
}

if (param_check('uid', $_GET)) {
  $firebase_url = 'https://PROJECT_ID.firebaseio.com';
  $database_secret = 'YOUR_FIREBASE_DATABASE_SECRET'; // https://console.firebase.google.com/project/_/settings/serviceaccounts/databasesecrets
  
  $file_path = getcwd() . '/../.backup/';
  $file_location = $file_path . '/'. $_GET['uid'] . '.json';

  if (!file_exists($file_location) || (filemtime($file_location) < time() - 86400) || array_key_exists('manual', $_GET) || array_key_exists('export', $_GET)) { // 1 day
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $firebase_url . '/users/' . $_GET['uid'] . '.json?format=export&auth=' . $database_secret, //REST
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET'
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
  
    if ($err) {
      echo '!! Error: Firebase backup.php: ' . $err;
    } else {
      if (!is_dir($file_path)) mkdir($file_path, 0777, true);
      file_put_contents($file_location, $response);

      $data = json_decode($response, true);
      unset($data['profile']['tier']);

      if (array_key_exists('export', $_GET)) {
        header('Content-type: application/json');
        header('Content-disposition: attachment; filename=bakomon-export-' . $_GET['uid'] . '.json');

        // Prevent caching
        header("Expires: Mon, 28 Sep 2020 06:21:26 GMT+8"); // Date in the past
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        echo json_encode($data);
      } else {
        echo 'Firebase backup file has been created successfully, size: ' . number_format(filesize($file_location) / 1024, 2) . ' KB';
      }
    }
  } else {
    date_default_timezone_set('Asia/Jakarta');
    echo 'Last firebase backup: ' . date('l, d F Y H:i:s O (e)', filemtime($file_location)) . ', size: ' . number_format(filesize($file_location) / 1024, 2) . ' KB';
  }
}
