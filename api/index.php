<?php

namespace Api;

require_once __DIR__ . '/Allowed.php';
require_once __DIR__ . '/Services/Route.php';
require_once __DIR__ . '/Controllers/Controller.php';

use Api\Allowed;
use Api\Services\Route;
use Api\Controllers\Controller;

$route = new Route;

// Prevent direct url access
if ((new Allowed)->check()) :
    if ($route->param_check('index', $_GET)) :
        $route->get($_GET['index'], Controller::class, $_GET['index'] . 'Page');
    endif;
else :
    http_response_code(403);
endif;
