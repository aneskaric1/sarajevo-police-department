<?php
// Officers and administrators (table users)

// GET /users  or  /users?status=pending (accounts an admin still has to approve)
Flight::route("GET /users", function () {
    $status = Flight::request()->query["status"] ?? null;
    if ($status !== null) {
        Flight::json(Flight::user_service()->get_by_status($status));
    } else {
        Flight::json(Flight::user_service()->get_all());
    }
});

Flight::route("GET /users/@id:[0-9]+", function ($id) {
    Flight::json(Flight::user_service()->get_by_id($id));
});

// Body: { "badge_number": "P-400", "first_name": "Lana", "last_name": "Hodzic",
//         "email": "lana.hodzic@spd.test", "password": "secret123", "officer_rank": "Patrol Officer" }
Flight::route("POST /users", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->add($data), 201);
});

Flight::route("PUT /users/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->update($data, $id));
});

Flight::route("DELETE /users/@id:[0-9]+", function ($id) {
    Flight::user_service()->delete($id);
    Flight::json(["message" => "Officer deleted."]);
});

// The tickets issued and the incidents handled by one officer
Flight::route("GET /users/@id:[0-9]+/tickets", function ($id) {
    Flight::json(Flight::ticket_service()->get_by_officer($id));
});

Flight::route("GET /users/@id:[0-9]+/incidents", function ($id) {
    Flight::json(Flight::incident_service()->get_by_officer($id));
});
