<?php
// Setup check. From the backend folder run:  php rest/dao/test.php
// It connects to the database and counts the rows of every table.

require_once __DIR__ . "/UserDao.php";
require_once __DIR__ . "/CitizenDao.php";
require_once __DIR__ . "/VehicleDao.php";
require_once __DIR__ . "/ViolationTypeDao.php";
require_once __DIR__ . "/TicketDao.php";
require_once __DIR__ . "/IncidentDao.php";
require_once __DIR__ . "/IncidentCitizenDao.php";

$daos = [
    "users"             => new UserDao(),
    "citizens"          => new CitizenDao(),
    "vehicles"          => new VehicleDao(),
    "violation_types"   => new ViolationTypeDao(),
    "tickets"           => new TicketDao(),
    "incidents"         => new IncidentDao(),
    "incident_citizens" => new IncidentCitizenDao(),
];

try {
    foreach ($daos as $table => $dao) {
        echo str_pad($table, 20) . $dao->count_all() . " rows\n";
    }
    echo "\nConnection OK. Expected with the sample data: 6, 8, 8, 6, 10, 8, 12.\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    echo "Check that MySQL is running, the database is imported and config.php is correct.\n";
}
