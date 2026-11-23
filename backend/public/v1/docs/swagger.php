<?php
/*
 * Generates the OpenAPI document (JSON) from the attributes in doc_setup.php and paths/*.php.
 * Swagger UI (index.html) loads this file. Open it directly to see the raw document.
 */
ini_set("display_errors", "0");     // one PHP warning inside the JSON would break Swagger UI

require __DIR__ . "/../../../vendor/autoload.php";

// The base URL of the API, computed from this request, so it is right everywhere:
// php -S localhost:8000          -> http://localhost:8000/backend/
// XAMPP                          -> http://localhost/police-department/backend/
// later the production server
$scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$backend_path = dirname($_SERVER["SCRIPT_NAME"], 4);           // /.../backend/public/v1/docs -> /.../backend
$backend_path = rtrim(str_replace("\\", "/", $backend_path), "/");
define("API_BASE_URL", "$scheme://{$_SERVER["HTTP_HOST"]}$backend_path/");

// swagger-php reads attributes with reflection, so the classes must be loaded first
require_once __DIR__ . "/doc_setup.php";
$files = glob(__DIR__ . "/paths/*.php");
foreach ($files as $file) {
    require_once $file;
}

$openapi = (new \OpenApi\Generator())->generate(array_merge([__DIR__ . "/doc_setup.php"], $files));

header("Content-Type: application/json");
echo $openapi->toJson();
