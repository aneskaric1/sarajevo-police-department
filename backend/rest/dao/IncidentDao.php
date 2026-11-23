<?php
require_once __DIR__ . "/BaseDao.php";

// incidents: cases reported to the police and handled by one officer.
class IncidentDao extends BaseDao
{
    // LEFT JOIN, because an incident may not have an officer yet
    private const SELECT = "
        SELECT i.*,
               u.badge_number AS officer_badge,
               CONCAT(u.first_name, ' ', u.last_name) AS officer_name
        FROM incidents i
        LEFT JOIN users u ON u.id = i.officer_id";

    public function __construct()
    {
        parent::__construct("incidents", [
            "case_number", "title", "type", "description",
            "location", "reported_at", "status", "officer_id",
        ]);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY i.reported_at DESC");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE i.id = :id", ["id" => $id]);
    }

    public function get_by_status($status)
    {
        return $this->query(self::SELECT . " WHERE i.status = :status ORDER BY i.reported_at DESC",
                            ["status" => $status]);
    }

    public function get_by_officer($officer_id)
    {
        return $this->query(self::SELECT . " WHERE i.officer_id = :officer_id ORDER BY i.reported_at DESC",
                            ["officer_id" => $officer_id]);
    }

    public function get_by_case_number($case_number)
    {
        return $this->query_unique("SELECT id, case_number FROM incidents WHERE case_number = :number",
                                   ["number" => $case_number]);
    }

    // The highest case number of one year, e.g. "INC-2026-0412" for "INC-2026-"
    public function get_last_case_number($prefix)
    {
        $row = $this->query_unique(
            "SELECT case_number FROM incidents WHERE case_number LIKE :prefix ORDER BY case_number DESC LIMIT 1",
            ["prefix" => $prefix . "%"]
        );
        return $row ? $row["case_number"] : null;
    }

    // Dashboard chart "Incidents by type"
    public function count_by_type()
    {
        return $this->query("SELECT type, COUNT(*) AS total FROM incidents GROUP BY type ORDER BY total DESC");
    }
}
