<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file (make sure this path exists and is writable)
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'config/config.php';

require_once 'core/Router.php';
require_once 'routes/web.php';

Router::dispatch(); // Dispatch the route



