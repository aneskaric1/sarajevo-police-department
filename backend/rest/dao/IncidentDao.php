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

    // Dashboard chart "Incidents by type"
    public function count_by_type()
    {
        return $this->query("SELECT type, COUNT(*) AS total FROM incidents GROUP BY type ORDER BY total DESC");
    }
}
