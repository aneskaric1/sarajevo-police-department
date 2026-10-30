<?php
require_once __DIR__ . "/BaseDao.php";

// incident_citizens: which citizens were involved in an incident, and how
// (victim, witness or suspect). Links incidents and citizens (many-to-many).
class IncidentCitizenDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("incident_citizens", ["incident_id", "citizen_id", "involvement"]);
    }

    // Everyone involved in one incident, with their names
    public function get_by_incident($incident_id)
    {
        return $this->query(
            "SELECT ic.*, c.first_name, c.last_name, c.national_id
             FROM incident_citizens ic
             JOIN citizens c ON c.id = ic.citizen_id
             WHERE ic.incident_id = :incident_id
             ORDER BY ic.involvement, c.last_name",
            ["incident_id" => $incident_id]
        );
    }

    // All incidents one citizen was involved in
    public function get_by_citizen($citizen_id)
    {
        return $this->query(
            "SELECT ic.*, i.case_number, i.title, i.status AS incident_status
             FROM incident_citizens ic
             JOIN incidents i ON i.id = ic.incident_id
             WHERE ic.citizen_id = :citizen_id
             ORDER BY i.reported_at DESC",
            ["citizen_id" => $citizen_id]
        );
    }

    // Stops adding the same citizen twice to the same incident
    public function exists($incident_id, $citizen_id)
    {
        return $this->query_unique(
            "SELECT id FROM incident_citizens WHERE incident_id = :incident_id AND citizen_id = :citizen_id",
            ["incident_id" => $incident_id, "citizen_id" => $citizen_id]
        ) !== null;
    }
}
