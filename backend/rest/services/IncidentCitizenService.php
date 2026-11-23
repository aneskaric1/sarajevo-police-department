<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/IncidentCitizenDao.php";
require_once __DIR__ . "/../dao/IncidentDao.php";
require_once __DIR__ . "/../dao/CitizenDao.php";

// Who was involved in an incident and how (victim, witness, suspect)
class IncidentCitizenService extends BaseService
{
    public const INVOLVEMENTS = ["victim", "witness", "suspect"];

    protected $entity_name = "Involvement record";
    private $incident_dao;
    private $citizen_dao;

    public function __construct()
    {
        parent::__construct(new IncidentCitizenDao());
        $this->incident_dao = new IncidentDao();
        $this->citizen_dao = new CitizenDao();
    }

    // GET /incidents/{id}/citizens
    public function get_by_incident($incident_id)
    {
        if ($this->incident_dao->get_by_id($incident_id) === null) {
            throw new NotFoundException("Incident not found.");
        }
        return $this->dao->get_by_incident($incident_id);
    }

    // GET /citizens/{id}/incidents
    public function get_by_citizen($citizen_id)
    {
        if ($this->citizen_dao->get_by_id($citizen_id) === null) {
            throw new NotFoundException("Citizen not found.");
        }
        return $this->dao->get_by_citizen($citizen_id);
    }

    protected function validate($link, $id = null)
    {
        Validator::required($link, ["incident_id", "citizen_id", "involvement"]);
        Validator::integer($link["incident_id"], "incident_id", 1);
        Validator::integer($link["citizen_id"], "citizen_id", 1);
        Validator::one_of($link["involvement"], "involvement", self::INVOLVEMENTS);

        $incident = $this->incident_dao->get_by_id($link["incident_id"]);
        if ($incident === null) {
            throw new ValidationException("Incident {$link["incident_id"]} does not exist.");
        }
        if ($incident["status"] === "closed" && $id === null) {
            throw new ValidationException("Incident {$incident["case_number"]} is closed; nobody can be added to it.");
        }
        if ($this->citizen_dao->get_by_id($link["citizen_id"]) === null) {
            throw new ValidationException("Citizen {$link["citizen_id"]} does not exist.");
        }
        // The same person only once per incident (also enforced by a UNIQUE key)
        $existing = $this->dao->get_link($link["incident_id"], $link["citizen_id"]);
        if ($existing && $existing["id"] != $id) {
            throw new ValidationException("This citizen is already recorded in incident {$incident["case_number"]}.");
        }
    }
}
