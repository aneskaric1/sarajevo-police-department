<?php
// CRUD test for every DAO. From the backend folder run:  php rest/dao/test_crud.php
// Everything runs inside one transaction that is rolled back at the end,
// so your sample data stays exactly as it was.

require_once __DIR__ . "/UserDao.php";
require_once __DIR__ . "/CitizenDao.php";
require_once __DIR__ . "/VehicleDao.php";
require_once __DIR__ . "/ViolationTypeDao.php";
require_once __DIR__ . "/TicketDao.php";
require_once __DIR__ . "/IncidentDao.php";
require_once __DIR__ . "/IncidentCitizenDao.php";

$passed = 0;
$failed = 0;

function check($label, $condition)
{
    global $passed, $failed;
    if ($condition) { $passed++; echo "[OK]   $label\n"; }
    else            { $failed++; echo "[FAIL] $label\n"; }
}

// add -> get_by_id -> update -> get_by_id -> delete -> get_by_id
function crud_test($name, $dao, $new_row, $change)
{
    echo "\n=== $name ===\n";
    $before = $dao->count_all();
    $row = $dao->add($new_row);
    check("add() returns a new id", $row["id"] > 0);
    check("count_all() went up by 1", $dao->count_all() === $before + 1);

    $read = $dao->get_by_id($row["id"]);
    $first_key = array_key_first($new_row);
    check("get_by_id() reads it back", $read !== null && $read[$first_key] == $new_row[$first_key]);

    $changed = $dao->update($change, $row["id"]);
    $read = $dao->get_by_id($row["id"]);
    $key = array_key_first($change);
    check("update() changed 1 row", $changed === 1 && $read[$key] == $change[$key]);

    check("delete() removed 1 row", $dao->delete($row["id"]) === 1);
    check("get_by_id() after delete is null", $dao->get_by_id($row["id"]) === null);
    return $row["id"];
}

$users      = new UserDao();
$citizens   = new CitizenDao();
$vehicles   = new VehicleDao();
$violations = new ViolationTypeDao();
$tickets    = new TicketDao();
$incidents  = new IncidentDao();
$links      = new IncidentCitizenDao();

$users->begin_transaction();
try {
    crud_test("UserDao", $users,
        ["badge_number" => "T-999", "first_name" => "Test", "last_name" => "Officer",
         "email" => "test.officer@spd.test", "password_hash" => password_hash("test1234", PASSWORD_BCRYPT),
         "hacker" => "this key is not a column and must be ignored"],
        ["officer_rank" => "Sergeant"]);
    check("get_all() never returns password_hash", !array_key_exists("password_hash", $users->get_all()[0]));
    check("get_by_email() returns password_hash for login", isset($users->get_by_email("admin@spd.test")["password_hash"]));
    check("admin password admin123 matches the hash",
        password_verify("admin123", $users->get_by_email("admin@spd.test")["password_hash"]));

    crud_test("CitizenDao", $citizens,
        ["first_name" => "Test", "last_name" => "Citizen", "national_id" => "9999999999999",
         "date_of_birth" => "2000-01-01", "phone" => "061 000 000", "address" => "Test street 1"],
        ["phone" => "062 111 111"]);
    check("search_by_name('Muj') finds Adnan Mujic", count($citizens->search_by_name("Muj")) === 1);
    check("search_by_name(\"' OR 1=1 -- \") finds nothing (no SQL injection)",
        count($citizens->search_by_name("' OR 1=1 -- ")) === 0);

    crud_test("VehicleDao", $vehicles,
        ["plate_number" => "X00-T-000", "make" => "Test", "model" => "Car", "colour" => "Green",
         "status" => "registered", "citizen_id" => 1],
        ["status" => "stolen"]);
    check("get_by_id() includes owner_name (JOIN)", $vehicles->get_by_id(1)["owner_name"] === "Adnan Mujic");
    check("get_stolen() returns 2 vehicles", count($vehicles->get_stolen()) === 2);
    check("get_by_citizen(1) returns 2 vehicles", count($vehicles->get_by_citizen(1)) === 2);

    crud_test("ViolationTypeDao", $violations,
        ["code" => "V-99", "name" => "Test violation", "standard_fine" => 10, "description" => "Test"],
        ["name" => "Changed violation"]);
    check("count_tickets(1) for Speeding is 3", $violations->count_tickets(1) === 3);

    crud_test("TicketDao", $tickets,
        ["ticket_number" => "TCK-TEST1", "citizen_id" => 1, "vehicle_id" => 1, "violation_type_id" => 1,
         "officer_id" => 2, "location" => "Test street", "issued_at" => "2026-10-29 10:00:00",
         "fine_amount" => 80, "status" => "unpaid"],
        ["status" => "paid"]);
    $ticket = $tickets->get_by_id(8);
    check("ticket without vehicle still loads (LEFT JOIN)", $ticket !== null && $ticket["plate_number"] === null);
    check("count_by_status('unpaid') is 4", $tickets->count_by_status("unpaid") === 4);
    check("get_by_officer(2) returns 4 tickets", count($tickets->get_by_officer(2)) === 4);

    crud_test("IncidentDao", $incidents,
        ["case_number" => "INC-TEST-1", "title" => "Test incident", "type" => "Other",
         "description" => "Test", "location" => "Test street", "reported_at" => "2026-10-29 10:00:00",
         "status" => "open", "officer_id" => 2],
        ["status" => "closed"]);
    $incident = $incidents->get_by_id(8);
    check("incident without officer still loads (LEFT JOIN)", $incident !== null && $incident["officer_badge"] === null);
    check("count_by_type() has Theft first with 3", $incidents->count_by_type()[0]["type"] === "Theft"
        && (int) $incidents->count_by_type()[0]["total"] === 3);

    crud_test("IncidentCitizenDao", $links,
        ["incident_id" => 8, "citizen_id" => 1, "involvement" => "witness"],
        ["involvement" => "suspect"]);
    check("get_by_incident(7) returns 3 people", count($links->get_by_incident(7)) === 3);
    check("exists(1, 2) is true", $links->exists(1, 2) === true);
    check("exists(1, 3) is false", $links->exists(1, 3) === false);
} catch (Throwable $e) {
    $failed++;
    echo "\n[ERROR] " . get_class($e) . ": " . $e->getMessage() . "\n";
} finally {
    $users->rollback();   // undo every change made by this test
}

echo "\nPassed: $passed, failed: $failed\n";
