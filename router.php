<?php
/**
 * Router for PHP built-in server
 * Serves static files directly, routes everything else to index.php
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

// Serve static files (CSS, JS, images) directly
if (is_file($file)) {
    return false;
}

// Route all other requests through the front controller
require __DIR__ . '/index.php';
