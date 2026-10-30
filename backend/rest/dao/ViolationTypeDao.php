<?php
require_once __DIR__ . "/BaseDao.php";

// violation_types: catalogue of violations with the standard fine.
class ViolationTypeDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("violation_types", ["code", "name", "standard_fine", "description"]);
    }

    public function get_all()
    {
        return $this->query("SELECT * FROM violation_types ORDER BY code");
    }

    public function get_by_code($code)
    {
        return $this->query_unique("SELECT * FROM violation_types WHERE code = :code", ["code" => $code]);
    }

    // How many tickets use this type (a type that is in use should not be deleted)
    public function count_tickets($id)
    {
        return (int) $this->query_unique(
            "SELECT COUNT(*) AS total FROM tickets WHERE violation_type_id = :id",
            ["id" => $id]
        )["total"];
    }
}
