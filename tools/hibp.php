<?php

require_once dirname(__DIR__) . '/api/Allowed.php';

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

if (param_check('pass', $_GET)) {
    // Check pawned passwords https://gist.github.com/JimWestergren/a4baf4716bfad6da989417a10e1ccc5f
    function checkPawnedPasswords(string $password)
    {
        $sha1 = strtoupper(sha1($password));
        $data = file_get_contents('https://api.pwnedpasswords.com/range/' . substr($sha1, 0, 5));
        if (FALSE !== strpos($data, substr($sha1, 5))) {
            $data = explode(substr($sha1, 5) . ':', $data);
            $count = (int) $data[1];
        }
        return $count ?? 0;
    }

    $pass = base64_decode($_GET['pass']);
    $res = checkPawnedPasswords($pass);
    $data = [
      'pawned' => $res,
      'message' => 'Password "' . $_GET['pass'] . '" has ' . ($res > 0 ? 'been leaked "' . $res . '" times.' : 'not been leaked.')
    ];
    header('Content-Type: application/json; charset=utf8');
    echo json_encode($data);
}
