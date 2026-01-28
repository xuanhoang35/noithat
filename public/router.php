<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$fullPath = __DIR__ . $path;
if ($path !== '/' && is_file($fullPath)) {
    return false;
}
// Route admin requests to admin front controller
if (str_starts_with($path, '/admin.php')) {
    require __DIR__ . '/../admin.php';
    return true;
}
require __DIR__ . '/../index.php';
