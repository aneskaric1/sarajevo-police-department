<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/CitizenDao.php";
require_once __DIR__ . "/../dao/VehicleDao.php";
require_once __DIR__ . "/../dao/TicketDao.php";
require_once __DIR__ . "/../dao/IncidentCitizenDao.php";

class CitizenService extends BaseService
{
    protected $entity_name = "Citizen";
    private $vehicle_dao;
    private $ticket_dao;
    private $incident_citizen_dao;

    public function __construct()
    {
        parent::__construct(new CitizenDao());
        $this->vehicle_dao = new VehicleDao();
        $this->ticket_dao = new TicketDao();
        $this->incident_citizen_dao = new IncidentCitizenDao();
    }

    public function search($text)
    {
        Validator::length($text, "search", 2, 50);
        return $this->dao->search_by_name(trim($text));
    }

    // A citizen with records cannot disappear: the records would lose their person
    public function delete($id)
    {
        $this->get_by_id($id);
        $vehicles  = count($this->vehicle_dao->get_by_citizen($id));
        $tickets   = count($this->ticket_dao->get_by_citizen($id));
        $incidents = count($this->incident_citizen_dao->get_by_citizen($id));
        if ($vehicles + $tickets + $incidents > 0) {
            throw new ValidationException(
                "This citizen still has $vehicles vehicle(s), $tickets ticket(s) and $incidents incident record(s). Remove them first."
            );
        }
        $this->dao->delete($id);
    }

    protected function validate($citizen, $id = null)
    {
        Validator::required($citizen, ["first_name", "last_name", "national_id", "date_of_birth"]);
        Validator::length($citizen["first_name"], "first_name", 2, 50);
        Validator::length($citizen["last_name"], "last_name", 2, 50);
        Validator::no_html($citizen["first_name"], "first_name");
        Validator::no_html($citizen["last_name"], "last_name");
        Validator::pattern($citizen["national_id"], "national_id", '/^[0-9]{13}$/', "0101990170012");
        Validator::date($citizen["date_of_birth"], "date_of_birth");
        Validator::not_in_future($citizen["date_of_birth"], "date_of_birth");
        if (isset($citizen["phone"]))   Validator::length($citizen["phone"], "phone", 6, 30);
        if (isset($citizen["address"])) {
            Validator::length($citizen["address"], "address", 3, 150);
            Validator::no_html($citizen["address"], "address");
        }

        $same = $this->dao->get_by_national_id($citizen["national_id"]);
        if ($same && $same["id"] != $id) {
            throw new ValidationException("A citizen with the national ID '{$citizen["national_id"]}' already exists.");
        }
    }
}
