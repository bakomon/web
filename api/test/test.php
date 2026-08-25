<?php

namespace Api\Test;

require_once dirname(__DIR__) . '/Allowed.php';
require_once dirname(__DIR__) . '/Services/Http.php';
require_once dirname(__DIR__, 2) . '/tools/curl.php';
require_once dirname(__DIR__, 2) . '/tools/faker/user-agent.php';

use Api\Allowed;
use Api\Services\Http;
use Tools\cURL;
use Faker\UserAgentGenerator;

// Prevent direct url access
if (!(new Allowed)->check()) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit;
}

$user_agent = [
  'desktop' => (new UserAgentGenerator)->userAgent(), //chrome
  'mobile' => (new UserAgentGenerator)->safariMobile(),
];

$source_link = $_GET['url'];
$method = $_GET['post'] == 'true' ? 'POST' : 'GET';
$headers = isset($_GET['headers']) ? json_decode(urldecode($_GET['headers']), true) : [];

$default_headers = [
  'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'User-Agent' => $user_agent[$_GET['mobile'] == 'true' ? 'mobile' : 'desktop'],
];
$headers = array_merge($default_headers, $headers); //override default headers

if ($_GET['curl'] == 'true') {
  if (strtoupper($method) === 'POST') {
    $source_xml = cURL::post($source_link, ['headers' => $headers, 'ignore_ssl' => true]);
  } else {
    $source_xml = cURL::get($source_link, ['headers' => $headers, 'ignore_ssl' => true]);
  }
  $status_code = $source_xml::$status;
  $data = [
    'status_code' => $status_code,
    'url' => $source_link,
    'headers' => $source_xml::$headers,
    'body' => $source_xml::$source,
  ];
} else {
  $source_xml = Http::load($source_link, ['method' => $method, 'headers' => $headers]);
  if (!$source_xml->isSuccess() && $_GET['bypass'] == 'true') {
    if ($source_xml->isBlocked()) $source_xml = Http::bypass($source_link, ['headers' => $headers]);
    // if ($source_xml->isBlocked()) $source_xml = Http::proxy($source_link, ['headers' => $headers]);
  }

  $status_code = $source_xml->status;

  if ($source_xml->error) {
    $data = [
      'status_code' => $status_code,
      'url' => $source_link,
      'error' => $source_xml->error,
    ];
  } else {
    $data = [
      'status_code' => $status_code,
      'url' => $source_link,
      'cache' => $source_xml->cache,
      'bypass' => $source_xml->bypass,
      'bypass_url' => $source_xml->bypass_url,
      'domain_change' => $source_xml->link,
      'headers' => $source_xml->headers,
      'body' => $source_xml->response(),
    ];
  }
}

http_response_code($status_code);
header('Content-Type: application/json; charset=utf8');
echo json_encode($data);
