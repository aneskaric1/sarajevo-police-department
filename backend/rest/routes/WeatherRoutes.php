<?php
// External API: patrol weather for Sarajevo from Open-Meteo.
// GET /weather  or  /weather?days=5   (1 to 7 days, default 3)
// 503 when Open-Meteo is not available; the rest of the API keeps working.
Flight::route("GET /weather", function () {
    $days = Flight::request()->query["days"] ?? 3;
    Flight::json(Flight::weather_service()->get_patrol_weather($days));
});
