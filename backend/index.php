<?php
/*
 * Entry point of the REST API. Every request comes here (.htaccess / server.php),
 * FlightPHP finds the matching route, the route calls one service method.
 */
require __DIR__ . "/vendor/autoload.php";               // FlightPHP (composer install)
require_once __DIR__ . "/utils/exceptions.php";

require_once __DIR__ . "/rest/services/UserService.php";
require_once __DIR__ . "/rest/services/CitizenService.php";
require_once __DIR__ . "/rest/services/VehicleService.php";
require_once __DIR__ . "/rest/services/ViolationTypeService.php";
require_once __DIR__ . "/rest/services/TicketService.php";
require_once __DIR__ . "/rest/services/IncidentService.php";
require_once __DIR__ . "/rest/services/IncidentCitizenService.php";
require_once __DIR__ . "/rest/services/WeatherService.php";

// Register every service ONCE; routes call Flight::ticket_service() etc.
Flight::register("user_service", "UserService");
Flight::register("citizen_service", "CitizenService");
Flight::register("vehicle_service", "VehicleService");
Flight::register("violation_type_service", "ViolationTypeService");
Flight::register("ticket_service", "TicketService");
Flight::register("incident_service", "IncidentService");
Flight::register("incident_citizen_service", "IncidentCitizenService");
Flight::register("weather_service", "WeatherService");

/* ONE place that turns every exception into a JSON answer */
Flight::map("error", function (Throwable $e) {
    if ($e instanceof HttpException) {          // thrown by a service: 400, 404, 503
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {                                    // PDOException, a bug, ...
        $status = 500;
        $message = "Something went wrong on the server. Please try again later.";
        error_log($e);                          // details only in the server log
    }
    Flight::response()
        ->clearBody()
        ->status($status)
        ->header("Content-Type", "application/json")
        ->write(json_encode(["error" => $message]))
        ->send();
});

/* A URL that matches no route */
Flight::map("notFound", function () {
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header("Content-Type", "application/json")
        ->write(json_encode(["error" => "Endpoint not found."]))
        ->send();
});

Flight::route("GET /", function () {
    Flight::json(["app" => "Sarajevo Police Department API", "message" => "The backend is running."]);
});

require_once __DIR__ . "/rest/routes/UserRoutes.php";
require_once __DIR__ . "/rest/routes/CitizenRoutes.php";
require_once __DIR__ . "/rest/routes/VehicleRoutes.php";
require_once __DIR__ . "/rest/routes/ViolationTypeRoutes.php";
require_once __DIR__ . "/rest/routes/TicketRoutes.php";
require_once __DIR__ . "/rest/routes/IncidentRoutes.php";
require_once __DIR__ . "/rest/routes/IncidentCitizenRoutes.php";
require_once __DIR__ . "/rest/routes/WeatherRoutes.php";

Flight::start();                                // always the LAST line
