<?php
// GET /incidents  or  /incidents?status=open
Flight::route("GET /incidents", function () {
    $status = Flight::request()->query["status"] ?? null;
    if ($status !== null) {
        Flight::json(Flight::incident_service()->get_by_status($status));
    } else {
        Flight::json(Flight::incident_service()->get_all());
    }
});

// Dashboard chart: how many incidents of each type
Flight::route("GET /incidents/stats", function () {
    Flight::json(Flight::incident_service()->count_by_type());
});

Flight::route("GET /incidents/@id:[0-9]+", function ($id) {
    Flight::json(Flight::incident_service()->get_by_id($id));
});

// Body: { "title": "Bicycle stolen", "type": "Theft", "location": "Ferhadija 5",
//         "description": "...", "officer_id": 2 }
// The server sets case_number, reported_at (now) and status (open) when they are not sent.
Flight::route("POST /incidents", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::incident_service()->add($data), 201);
});

Flight::route("PUT /incidents/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::incident_service()->update($data, $id));
});

Flight::route("DELETE /incidents/@id:[0-9]+", function ($id) {
    Flight::incident_service()->delete($id);
    Flight::json(["message" => "Incident deleted."]);
});

// The people involved in one incident
Flight::route("GET /incidents/@id:[0-9]+/citizens", function ($id) {
    Flight::json(Flight::incident_citizen_service()->get_by_incident($id));
});
