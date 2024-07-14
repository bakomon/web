<?php

function param_check($name, $arr)
{
  return isset($arr[$name]) && (!empty($arr[$name]) || $arr[$name] != '');
}

if (param_check('ref', $_GET) && param_check('url', $_GET)) {
  $referer = $_GET['ref'];
  $imageUrl = $_GET['url'];

  // Create a stream context with the referer header
  $options = [
    'http' => [
        'header' => "Referer: $referer",
    ],
  ];
  $context = stream_context_create($options);
  $imageData = @file_get_contents($imageUrl, false, $context);

  if ($imageData !== false) {
    header('Content-Type: ' . getimagesizefromstring($imageData)['mime']);
    echo $imageData;
  } else {
    echo '!! Error: Unable to load image';
  }
}
