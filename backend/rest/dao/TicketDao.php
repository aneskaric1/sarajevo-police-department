<?php
require_once __DIR__ . "/BaseDao.php";

// tickets: a fine issued by an officer to a citizen for a violation.
class TicketDao extends BaseDao
{
    // JOINs bring the names the tickets table shows in the frontend.
    // LEFT JOIN for vehicles, because a ticket can have no vehicle.
    private const SELECT = "
        SELECT t.*,
               CONCAT(c.first_name, ' ', c.last_name) AS citizen_name,
               v.plate_number,
               vt.code AS violation_code, vt.name AS violation_name,
               u.badge_number AS officer_badge,
               CONCAT(u.first_name, ' ', u.last_name) AS officer_name
        FROM tickets t
        JOIN citizens c         ON c.id = t.citizen_id
        LEFT JOIN vehicles v    ON v.id = t.vehicle_id
        JOIN violation_types vt ON vt.id = t.violation_type_id
        JOIN users u            ON u.id = t.officer_id";

    public function __construct()
    {
        parent::__construct("tickets", [
            "ticket_number", "citizen_id", "vehicle_id", "violation_type_id",
            "officer_id", "location", "issued_at", "fine_amount", "status",
        ]);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY t.issued_at DESC");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE t.id = :id", ["id" => $id]);
    }

    public function get_by_status($status)
    {
        return $this->query(self::SELECT . " WHERE t.status = :status ORDER BY t.issued_at DESC",
                            ["status" => $status]);
    }

    public function get_by_citizen($citizen_id)
    {
        return $this->query(self::SELECT . " WHERE t.citizen_id = :citizen_id ORDER BY t.issued_at DESC",
                            ["citizen_id" => $citizen_id]);
    }

    public function get_by_officer($officer_id)
    {
        return $this->query(self::SELECT . " WHERE t.officer_id = :officer_id ORDER BY t.issued_at DESC",
                            ["officer_id" => $officer_id]);
    }

    // Dashboard: e.g. how many tickets are still unpaid
    public function count_by_status($status)
    {
        return (int) $this->query_unique(
            "SELECT COUNT(*) AS total FROM tickets WHERE status = :status",
            ["status" => $status]
        )["total"];
    }
}
