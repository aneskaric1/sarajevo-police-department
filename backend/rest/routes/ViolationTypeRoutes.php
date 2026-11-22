<?php
Flight::route("GET /violation-types", function () {
    Flight::json(Flight::violation_type_service()->get_all());
});

Flight::route("GET /violation-types/@id:[0-9]+", function ($id) {
    Flight::json(Flight::violation_type_service()->get_by_id($id));
});

// Body: { "code": "V-07", "name": "Driving without a licence", "standard_fine": 200, "description": "..." }
Flight::route("POST /violation-types", function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::violation_type_service()->add($data), 201);
});

Flight::route("PUT /violation-types/@id:[0-9]+", function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::violation_type_service()->update($data, $id));
});

Flight::route("DELETE /violation-types/@id:[0-9]+", function ($id) {
    Flight::violation_type_service()->delete($id);
    Flight::json(["message" => "Violation type deleted."]);
});
