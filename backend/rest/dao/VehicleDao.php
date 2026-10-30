<?php
require_once __DIR__ . "/BaseDao.php";

// vehicles: every vehicle belongs to one citizen.
class VehicleDao extends BaseDao
{
    // One query with a JOIN brings the owner's name along (no query per vehicle)
    private const SELECT = "
        SELECT v.*, CONCAT(c.first_name, ' ', c.last_name) AS owner_name
        FROM vehicles v
        JOIN citizens c ON c.id = v.citizen_id";

    public function __construct()
    {
        parent::__construct("vehicles", [
            "plate_number", "make", "model", "colour", "status", "citizen_id",
        ]);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY v.plate_number");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE v.id = :id", ["id" => $id]);
    }

    public function get_by_plate($plate_number)
    {
        return $this->query_unique(self::SELECT . " WHERE v.plate_number = :plate", ["plate" => $plate_number]);
    }

    public function get_by_citizen($citizen_id)
    {
        return $this->query(self::SELECT . " WHERE v.citizen_id = :citizen_id ORDER BY v.plate_number",
                            ["citizen_id" => $citizen_id]);
    }

    public function get_stolen()
    {
        return $this->query(self::SELECT . " WHERE v.status = 'stolen' ORDER BY v.plate_number");
    }
}
