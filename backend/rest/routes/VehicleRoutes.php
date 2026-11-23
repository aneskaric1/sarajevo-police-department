<?php
// GET /vehicles  or  /vehicles?status=stolen  or  /vehicles?plate=A12-K-345
Flight::route("GET /vehicles", function () {
    $query = Flight::request()->query;
    if (isset($query["plate"])) {
        Flight::json(Flight::vehicle_service()->get_by_plate($query["plate"]));
    } elseif (($query["status"] ?? null) === "stolen") {
        Flight::json(Flight::vehicle_service()->get_stolen());
    } else {
        Flight::json(Flight::vehicle_service()->get_all());
    }
});

Flight::route("GET /vehicles/@id:[0-9]+", function ($id) {
    Flight::json(Flight::vehicle_service()->get_by_id($id));
});

// Body: { "plate_number": "A99-K-111", "make": "Skoda", "model": "Fabia",
//         "colour": "White", "status": "registered", "citizen_id": 2 }
Flight::route("POST /vehicles", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::vehicle_service()->add($data), 201);
});

Flight::route("PUT /vehicles/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::vehicle_service()->update($data, $id));
});

Flight::route("DELETE /vehicles/@id:[0-9]+", function ($id) {
    Flight::vehicle_service()->delete($id);
    Flight::json(["message" => "Vehicle deleted."]);
});
