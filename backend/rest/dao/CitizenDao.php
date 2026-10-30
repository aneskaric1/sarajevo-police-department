<?php
require_once __DIR__ . "/BaseDao.php";

// citizens: people known to the department.
class CitizenDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("citizens", [
            "first_name", "last_name", "national_id", "date_of_birth", "phone", "address",
        ]);
    }

    public function get_all()
    {
        return $this->query("SELECT * FROM citizens ORDER BY last_name, first_name");
    }

    public function get_by_national_id($national_id)
    {
        return $this->query_unique(
            "SELECT * FROM citizens WHERE national_id = :national_id",
            ["national_id" => $national_id]
        );
    }

    // Search in first or last name. The % signs are added to the value,
    // never to the SQL text. Each named placeholder is used once.
    public function search_by_name($text)
    {
        $pattern = "%" . $text . "%";
        return $this->query(
            "SELECT * FROM citizens
             WHERE first_name LIKE :first OR last_name LIKE :last
             ORDER BY last_name, first_name",
            ["first" => $pattern, "last" => $pattern]
        );
    }
}
