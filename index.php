<?php

namespace API;

use API\Config\Config;

use PAF\Model\Database;
use PAF\Router\Response;
use PAF\Router\Router;

error_reporting(E_ALL ^ E_WARNING);

try {
    // Libraries
    require_once __DIR__ . '/lib/PAF/src/autoload.php';
    require_once __DIR__ . '/lib/PHP-JWT/JWT.php';

    // Autoload
    require_once __DIR__ . '/autoload.php';

    // Database
    Database::setDatabase(
        'mysql',
        Config::get('database.host'),
        Config::get('database.database'),
        Config::get('database.user'),
        Config::get('database.password'),
        Config::get('database.charset')
    );

    // Constants
    define('ROOT_URL', Config::get('root_url'));
    define('VERSION', Config::get('version'));

    // Router
    Router::setHeaders([
        "Access-Control-Allow-Headers" => "Content-Type, Authorization",
    ]);

    Router::init(ROOT_URL, true);

    // Routes

    try {
        Router::addRoutes()->get('/', function () {
            return "API v" . VERSION;
        });

        Router::group('/auth', __DIR__ . '/routes/auth/index.php');

        // TODO: include routes

        if (!Router::execute()) {
            throw new \Exception('Method not found');
        }
    } catch (\Exception $e) {
        Router::output(Response::badRequest($e->getMessage()));
    }
} catch (\Exception $e) {
    @header("Content-Type: application/json");
    @http_response_code(500);

    echo json_encode($e->getMessage());
}
