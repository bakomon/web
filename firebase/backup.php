<?php
// Source: https://github.com/front/firebase-backup/blob/15429344f333d1fc1b6bb0cbf355a9ed18ba8118/php/index.php

require_once dirname(__DIR__) . '/api/Allowed.php';
require_once dirname(__DIR__) . '/tools/curl.php';
require_once dirname(__DIR__) . '/tools/dot-env.php';

use \Api\Allowed;
use Tools\cURL;
use Tools\DotEnv;

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

// Prevent direct url access
if (!(new Allowed)->check(['only_referer' => true])) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit;
}

function param_check($name, $arr)
{
  return isset($arr[$name]) && (!empty($arr[$name]) || $arr[$name] != '');
}

if (param_check('uid', $_GET)) {
  $timezone = param_check('tz', $_GET) ? $_GET['tz'] : 'UTC';
  date_default_timezone_set($timezone);

  if (!param_check('FIREBASE_DATABASE_URL', $_ENV) || !param_check('FIREBASE_DATABASE_SECRET', $_ENV)) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    echo '!! Error: Firebase backup.php: Missing Firebase configuration in .env file.';
    exit;
  }

  $database_url = $_ENV['FIREBASE_DATABASE_URL']; //https://console.firebase.google.com/project/_/database
  $database_secret = $_ENV['FIREBASE_DATABASE_SECRET']; //https://console.firebase.google.com/project/_/settings/serviceaccounts/databasesecrets

  $file_path = getcwd() . '/.backup/';
  $file_location = $file_path . '/'. $_GET['uid'] . '.json';

  if (!file_exists($file_location) || (filemtime($file_location) < time() - 86400) || array_key_exists('manual', $_GET) || array_key_exists('export', $_GET)) { // 1 day
    $url = $database_url . '/users/' . $_GET['uid'] . '.json?format=export&auth=' . $database_secret;

    $response = false;
    // Try "file_get_contents" if "allow_url_fopen" is enabled
    if (ini_get('allow_url_fopen')) {
      $response = @file_get_contents($url);
    }

    // Fallback to cURL if "file_get_contents" fails
    if ($response === false) {
      $curl = cURL::get($url, ['ignore_ssl' => true]);
      $response = cURL::$source;
    }

    if ($response === false || empty($response)) {
      $error = error_get_last();
      $error_msg = isset($error['message']) ? $error['message'] : 'Unknown error';
      header('HTTP/1.1 500 Internal Server Error', true, 500);
      echo '!! Error: Firebase backup.php: ' . $error_msg;
    } else {
      try {
        if (!is_dir($file_path)) {
          if (!mkdir($file_path, 0777, true)) {
            throw new \Exception('Failed to create backup directory: ' . $file_path);
          }
        }

        if (file_put_contents($file_location, $response) === false) {
          throw new \Exception('Failed to write backup file: ' . $file_location);
        }
      } catch (\Exception $e) {
        header('HTTP/1.1 500 Internal Server Error', true, 500);
        echo '!! Error: Firebase backup.php: ' . $e->getMessage();
        exit;
      }

      $data = json_decode($response, true);
      unset($data['profile']['tier']);

      header('Content-type: application/json');
      if (array_key_exists('export', $_GET)) {
        header('Content-disposition: attachment; filename=bakomon-export-' . $_GET['uid'] . '-' . filemtime($file_location) . '.json');

        // Prevent caching
        header("Expires: Mon, 28 Sep 2020 06:21:26 GMT+8"); // Date in the past
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        echo json_encode($data);
      } else {
        echo json_encode([
          'message' => 'Firebase backup file has been created successfully',
          'size' => number_format(filesize($file_location) / 1024, 2) . ' KB',
        ]);
      }
    }
  } else {
    header('Content-type: application/json');
    $last_backup = date('l, d F Y H:i:s O (e)', filemtime($file_location));
    echo json_encode([
      'message' => 'Last firebase backup: ' . $last_backup,
      'date' => $last_backup,
      'size' => number_format(filesize($file_location) / 1024, 2) . ' KB',
    ]);
  }
}
