<?php

namespace Api\Test;

require_once '../Services/Http.php';
require_once '../Allowed.php';

use Api\Services\Http;
use Api\Allowed;

// Prevent direct url access
if (!(new Allowed)->check()) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit;
}

$source_link = $_GET['url'];

$user_agent = [ //chrome
  'desktop' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
  'mobile' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.186 Mobile Safari/537.36',
];

$headers = [
  'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'User-Agent' => $user_agent[$_GET['mobile'] == 'true' ? 'mobile' : 'desktop'],
];

$source_xml = Http::load($source_link, ['headers' => $headers]);
if (!$source_xml->isSuccess() && $_GET['bypass'] == 'true') {
  if ($source_xml->isBlocked()) $source_xml = Http::bypass($source_link);
}

$status_code = $source_xml->status;

if ($source_xml->error) {
  $data = array(
    'status_code' => $status_code,
    'url' => $source_link,
    'error' => $source_xml->error,
  );
} else {
  $data = array(
    'status_code' => $status_code,
    'url' => $source_link,
    'cache' => $source_xml->cache,
    'bypass' => $source_xml->bypass,
    'domain_change' => $source_xml->link,
    'headers' => $source_xml->headers,
    'body' => $source_xml->response(),
  );
}


http_response_code($status_code);
header('Content-Type: application/json; charset=utf8');
echo json_encode($data);
