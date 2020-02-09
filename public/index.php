<?php

namespace BasicAPI;

use App\MyRoutes;
use Exception;
use Monolog\Logger;
use Throwable;

define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..');
require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'BasicAPI' . DIRECTORY_SEPARATOR . 'FrontController.php');

try {
    //set your routes class here
    $front_controller = new FrontController(new MyRoutes(), new Response(), new Access());
    $front_controller->run();
} catch (Exception|Throwable  $e) {
    if (!headers_sent()) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
    }
    echo json_encode(['message' => 'Critical Error: ' . $e->getMessage()]);
    /** @var Logger $logger */
    $logger = Registry::get('logger');
    $logger->critical(
        json_encode(
            $e->getMessage() . " Where: " . $e->getFile() . " (" . $e->getLine(
            ) . "), IP: " . @$_SERVER['REMOTE_ADDR'] . " URI: " . @$_SERVER['REQUEST_URI']
        )
    );
}

