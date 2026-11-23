<?php
/*
 * Router for PHP's built-in web server (when you do not use XAMPP/Apache).
 * Start it in the main project folder:   php -S localhost:8000 server.php
 *   http://localhost:8000/frontend/                 the single-page app
 *   http://localhost:8000/backend/                  the REST API (FlightPHP)
 *   http://localhost:8000/backend/public/v1/docs/   Swagger UI
 */
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path === "/") {
    header("Location: /frontend/");
    return true;
}

// The frontend and backend/public (Swagger UI) are real files: serve them as they are
$is_public = str_starts_with($path, "/frontend") || str_starts_with($path, "/backend/public/");
if ($is_public && (is_file($file) || is_dir($file))) {
    return false;
}

// Every other /backend URL goes to FlightPHP, which then sees /backend as its base.
// So the PHP files of the DAOs and services can never be opened from the browser.
if (str_starts_with($path, "/backend")) {
    $_SERVER["SCRIPT_NAME"] = "/backend/index.php";
    $_SERVER["SCRIPT_FILENAME"] = __DIR__ . "/backend/index.php";
    require __DIR__ . "/backend/index.php";
    return true;
}

http_response_code(404);
echo "Not found";
