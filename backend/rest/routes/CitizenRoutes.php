<?php
// GET /citizens  or  /citizens?search=muj
Flight::route("GET /citizens", function () {
    $search = Flight::request()->query["search"] ?? null;
    if ($search !== null) {
        Flight::json(Flight::citizen_service()->search($search));
    } else {
        Flight::json(Flight::citizen_service()->get_all());
    }
});

Flight::route("GET /citizens/@id:[0-9]+", function ($id) {
    Flight::json(Flight::citizen_service()->get_by_id($id));
});

// Body: { "first_name": "Mirza", "last_name": "Hodzic", "national_id": "0505990170033",
//         "date_of_birth": "1990-05-05", "phone": "061 555 909", "address": "Titova 10, Sarajevo" }
Flight::route("POST /citizens", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::citizen_service()->add($data), 201);
});

Flight::route("PUT /citizens/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::citizen_service()->update($data, $id));
});

Flight::route("DELETE /citizens/@id:[0-9]+", function ($id) {
    Flight::citizen_service()->delete($id);
    Flight::json(["message" => "Citizen deleted."]);
});

// Everything recorded about one citizen
Flight::route("GET /citizens/@id:[0-9]+/vehicles", function ($id) {
    Flight::json(Flight::vehicle_service()->get_by_citizen($id));
});

Flight::route("GET /citizens/@id:[0-9]+/tickets", function ($id) {
    Flight::json(Flight::ticket_service()->get_by_citizen($id));
});

Flight::route("GET /citizens/@id:[0-9]+/incidents", function ($id) {
    Flight::json(Flight::incident_citizen_service()->get_by_citizen($id));
});
