<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/IncidentDao.php";
require_once __DIR__ . "/../dao/UserDao.php";

class IncidentService extends BaseService
{
    public const STATUSES = ["open", "investigating", "closed"];
    public const TYPES = ["Theft", "Traffic accident", "Disturbance", "Vandalism", "Assault", "Vehicle theft", "Other"];

    protected $entity_name = "Incident";
    private $user_dao;

    public function __construct()
    {
        parent::__construct(new IncidentDao());
        $this->user_dao = new UserDao();
    }

    public function get_by_status($status)
    {
        Validator::one_of($status, "status", self::STATUSES);
        return $this->dao->get_by_status($status);
    }

    public function get_by_officer($officer_id)
    {
        if ($this->user_dao->get_by_id($officer_id) === null) {
            throw new NotFoundException("Officer not found.");
        }
        return $this->dao->get_by_officer($officer_id);
    }

    public function count_by_type()
    {
        return $this->dao->count_by_type();
    }

    // The case number (INC-2026-0413) and the time are given by the server
    public function add($incident)
    {
        $incident = $this->normalize($incident);
        $incident["case_number"] = $this->next_case_number();
        $incident["reported_at"] = $incident["reported_at"] ?? date("Y-m-d H:i:s");
        $incident["status"] = $incident["status"] ?? "open";
        $this->validate($incident);
        $created = $this->dao->add($incident);
        return $this->get_by_id($created["id"]);
    }

    public function update($incident, $id)
    {
        unset($incident["case_number"]);
        return parent::update($incident, $id);
    }

    protected function normalize($incident)
    {
        $incident = parent::normalize($incident);
        if (isset($incident["reported_at"])) $incident["reported_at"] = $this->normalize_datetime($incident["reported_at"]);
        return $incident;
    }

    protected function validate($incident, $id = null)
    {
        Validator::required($incident, ["title", "type", "location"]);
        Validator::length($incident["title"], "title", 3, 120);
        Validator::no_html($incident["title"], "title");
        Validator::one_of($incident["type"], "type", self::TYPES);
        Validator::length($incident["location"], "location", 3, 150);
        Validator::no_html($incident["location"], "location");
        if (isset($incident["description"])) {
            Validator::length($incident["description"], "description", 0, 2000);
            Validator::no_html($incident["description"], "description");
        }
        Validator::datetime($incident["reported_at"], "reported_at");
        Validator::not_in_future($incident["reported_at"], "reported_at");
        Validator::one_of($incident["status"], "status", self::STATUSES);

        // An incident under investigation must have an officer
        if ($incident["status"] === "investigating" && empty($incident["officer_id"])) {
            throw new ValidationException("An incident under investigation must have an assigned officer.");
        }
        if (!empty($incident["officer_id"])) {
            Validator::integer($incident["officer_id"], "officer_id", 1);
            $officer = $this->user_dao->get_by_id($incident["officer_id"]);
            if ($officer === null) {
                throw new ValidationException("Officer {$incident["officer_id"]} does not exist.");
            }
            if ($officer["status"] !== "active") {
                throw new ValidationException("Officer {$officer["badge_number"]} is not active yet and cannot handle incidents.");
            }
        }
    }

    // INC-2026-0412 -> INC-2026-0413; a new year starts again at 0001
    private function next_case_number()
    {
        $prefix = "INC-" . date("Y") . "-";
        $last = $this->dao->get_last_case_number($prefix);
        $number = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad((string) $number, 4, "0", STR_PAD_LEFT);
    }
}
