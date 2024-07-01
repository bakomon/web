<?php

namespace Api\Test;

require_once '../Services/Http.php';
require_once '../Allowed.php';

use Api\Services\Http;
use Api\Allowed;

if (!(new Allowed)->check()) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit;
}

$source_link = $_GET['url'];

$source_xml = Http::get($source_link, ['headers' => ['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8']]);
if (!$source_xml->isSuccess()) {
  if ($source_xml->isBlocked()) $source_xml = Http::bypass($source_link);
}

$status_code = $source_xml::$status;

$data = [
  'status_code' => $status_code,
  'url' => $source_link,
  'bypass' => $source_xml::$bypass,
  'headers' => $source_xml::$headers,
  'body' => $source_xml::$source,
];

http_response_code($status_code);
header('Content-Type: application/json; charset=utf8');
if (isset($_SERVER['HTTP_ORIGIN'])) header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']); //plugin
echo json_encode($data);
