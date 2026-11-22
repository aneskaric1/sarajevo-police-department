<?php
// GET /tickets  or  /tickets?status=unpaid
Flight::route("GET /tickets", function () {
    $status = Flight::request()->query["status"] ?? null;
    if ($status !== null) {
        Flight::json(Flight::ticket_service()->get_by_status($status));
    } else {
        Flight::json(Flight::ticket_service()->get_all());
    }
});

Flight::route("GET /tickets/@id:[0-9]+", function ($id) {
    Flight::json(Flight::ticket_service()->get_by_id($id));
});

// Body: { "citizen_id": 1, "vehicle_id": 1, "violation_type_id": 1, "officer_id": 2,
//         "location": "Zmaja od Bosne, Marijin Dvor" }
// The server sets ticket_number, issued_at (now), status (unpaid) and,
// when it is not sent, fine_amount (the standard fine of the violation).
Flight::route("POST /tickets", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ticket_service()->add($data), 201);
});

Flight::route("PUT /tickets/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ticket_service()->update($data, $id));
});

Flight::route("DELETE /tickets/@id:[0-9]+", function ($id) {
    Flight::ticket_service()->delete($id);
    Flight::json(["message" => "Ticket deleted."]);
});

// Actions on one ticket get their own sub-path
Flight::route("PUT /tickets/@id:[0-9]+/pay", function ($id) {
    Flight::json(Flight::ticket_service()->pay($id));
});

Flight::route("PUT /tickets/@id:[0-9]+/cancel", function ($id) {
    Flight::json(Flight::ticket_service()->cancel($id));
});
