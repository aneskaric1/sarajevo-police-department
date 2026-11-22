<?php
// Links between incidents and citizens (victim, witness, suspect)

Flight::route("GET /incident-citizens", function () {
    Flight::json(Flight::incident_citizen_service()->get_all());
});

Flight::route("GET /incident-citizens/@id:[0-9]+", function ($id) {
    Flight::json(Flight::incident_citizen_service()->get_by_id($id));
});

// Body: { "incident_id": 8, "citizen_id": 1, "involvement": "witness" }
Flight::route("POST /incident-citizens", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::incident_citizen_service()->add($data), 201);
});

Flight::route("PUT /incident-citizens/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::incident_citizen_service()->update($data, $id));
});

Flight::route("DELETE /incident-citizens/@id:[0-9]+", function ($id) {
    Flight::incident_citizen_service()->delete($id);
    Flight::json(["message" => "Involvement record deleted."]);
});
