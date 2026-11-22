<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/VehicleDao.php";
require_once __DIR__ . "/../dao/CitizenDao.php";

class VehicleService extends BaseService
{
    public const STATUSES = ["registered", "stolen"];

    protected $entity_name = "Vehicle";
    private $citizen_dao;

    public function __construct()
    {
        parent::__construct(new VehicleDao());
        $this->citizen_dao = new CitizenDao();
    }

    public function get_stolen()
    {
        return $this->dao->get_stolen();
    }

    // 404 when the citizen does not exist, so "no vehicles" ([]) and
    // "no such citizen" are different answers
    public function get_by_citizen($citizen_id)
    {
        if ($this->citizen_dao->get_by_id($citizen_id) === null) {
            throw new NotFoundException("Citizen not found.");
        }
        return $this->dao->get_by_citizen($citizen_id);
    }

    public function get_by_plate($plate_number)
    {
        $vehicle = $this->dao->get_by_plate(strtoupper(trim($plate_number)));
        if ($vehicle === null) {
            throw new NotFoundException("No vehicle with the plate '$plate_number'.");
        }
        return $vehicle;
    }

    // Plates are stored in capitals, so "a12-k-345" and "A12-K-345" are the same car
    protected function normalize($vehicle)
    {
        $vehicle = parent::normalize($vehicle);
        if (isset($vehicle["plate_number"])) {
            $vehicle["plate_number"] = strtoupper($vehicle["plate_number"]);
        }
        return $vehicle;
    }

    protected function validate($vehicle, $id = null)
    {
        Validator::required($vehicle, ["plate_number", "make", "model", "citizen_id"]);
        Validator::pattern($vehicle["plate_number"], "plate_number", '/^[A-Z0-9]{3}-[A-Z]-[0-9]{3}$/', "A12-K-345");
        Validator::length($vehicle["make"], "make", 2, 50);
        Validator::length($vehicle["model"], "model", 1, 50);
        if (isset($vehicle["colour"])) Validator::length($vehicle["colour"], "colour", 3, 30);
        if (isset($vehicle["status"])) Validator::one_of($vehicle["status"], "status", self::STATUSES);
        Validator::integer($vehicle["citizen_id"], "citizen_id", 1);

        if ($this->citizen_dao->get_by_id($vehicle["citizen_id"]) === null) {
            throw new ValidationException("Citizen {$vehicle["citizen_id"]} does not exist.");
        }
        $same = $this->dao->get_by_plate($vehicle["plate_number"]);
        if ($same && $same["id"] != $id) {
            throw new ValidationException("A vehicle with the plate '{$vehicle["plate_number"]}' is already registered.");
        }
    }
}
