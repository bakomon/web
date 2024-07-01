<?php

namespace Api;

require_once __DIR__ . '/Services/Route.php';
require_once __DIR__ . '/CController.php';
require_once __DIR__ . '/Allowed.php';

use Api\Services\Route;
use Api\CController;
use Api\Allowed;

$route = new Route;

if ((new Allowed)->check() || isset($_GET['dev'])) :
    if ($route->param_check('index', $_GET)) :
        $route->get($_GET['index'], CController::class, $_GET['index'] . 'Page');
    endif;
else :
    http_response_code(403);
endif;
