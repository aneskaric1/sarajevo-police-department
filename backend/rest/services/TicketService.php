<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/TicketDao.php";
require_once __DIR__ . "/../dao/CitizenDao.php";
require_once __DIR__ . "/../dao/VehicleDao.php";
require_once __DIR__ . "/../dao/ViolationTypeDao.php";
require_once __DIR__ . "/../dao/UserDao.php";

class TicketService extends BaseService
{
    public const STATUSES = ["unpaid", "paid", "contested", "cancelled"];

    protected $entity_name = "Ticket";
    private $citizen_dao;
    private $vehicle_dao;
    private $violation_dao;
    private $user_dao;

    public function __construct()
    {
        parent::__construct(new TicketDao());
        $this->citizen_dao = new CitizenDao();
        $this->vehicle_dao = new VehicleDao();
        $this->violation_dao = new ViolationTypeDao();
        $this->user_dao = new UserDao();
    }

    // GET /tickets?status=unpaid
    public function get_by_status($status)
    {
        Validator::one_of($status, "status", self::STATUSES);
        return $this->dao->get_by_status($status);
    }

    public function get_by_citizen($citizen_id)
    {
        if ($this->citizen_dao->get_by_id($citizen_id) === null) {
            throw new NotFoundException("Citizen not found.");
        }
        return $this->dao->get_by_citizen($citizen_id);
    }

    public function get_by_officer($officer_id)
    {
        if ($this->user_dao->get_by_id($officer_id) === null) {
            throw new NotFoundException("Officer not found.");
        }
        return $this->dao->get_by_officer($officer_id);
    }

    // The server fills in what the officer should not have to type:
    // the next ticket number, the time and the standard fine of the violation
    public function add($ticket)
    {
        $ticket = $this->normalize($ticket);
        $ticket["ticket_number"] = $this->next_ticket_number();
        $ticket["issued_at"] = $ticket["issued_at"] ?? date("Y-m-d H:i:s");
        $ticket["status"] = "unpaid";                 // a new ticket is always unpaid
        if (!isset($ticket["fine_amount"]) && isset($ticket["violation_type_id"])) {
            $violation = $this->violation_dao->get_by_id($ticket["violation_type_id"]);
            if ($violation) $ticket["fine_amount"] = $violation["standard_fine"];
        }
        $this->validate($ticket);
        $created = $this->dao->add($ticket);
        return $this->get_by_id($created["id"]);
    }

    // The ticket number never changes after it is issued
    public function update($ticket, $id)
    {
        unset($ticket["ticket_number"]);
        return parent::update($ticket, $id);
    }

    // PUT /tickets/{id}/pay
    public function pay($id)
    {
        $ticket = $this->get_by_id($id);
        if (!in_array($ticket["status"], ["unpaid", "contested"], true)) {
            throw new ValidationException("Only an unpaid or contested ticket can be paid (this one is {$ticket["status"]}).");
        }
        $this->dao->update(["status" => "paid"], $id);
        return $this->get_by_id($id);
    }

    // PUT /tickets/{id}/cancel
    public function cancel($id)
    {
        $ticket = $this->get_by_id($id);
        if ($ticket["status"] === "paid") {
            throw new ValidationException("A paid ticket cannot be cancelled.");
        }
        if ($ticket["status"] === "cancelled") {
            throw new ValidationException("This ticket is already cancelled.");
        }
        $this->dao->update(["status" => "cancelled"], $id);
        return $this->get_by_id($id);
    }

    protected function normalize($ticket)
    {
        $ticket = parent::normalize($ticket);
        if (isset($ticket["issued_at"])) $ticket["issued_at"] = $this->normalize_datetime($ticket["issued_at"]);
        return $ticket;
    }

    protected function validate($ticket, $id = null)
    {
        Validator::required($ticket, ["citizen_id", "violation_type_id", "officer_id", "location", "fine_amount"]);
        Validator::integer($ticket["citizen_id"], "citizen_id", 1);
        Validator::integer($ticket["violation_type_id"], "violation_type_id", 1);
        Validator::integer($ticket["officer_id"], "officer_id", 1);
        Validator::length($ticket["location"], "location", 3, 150);
        Validator::no_html($ticket["location"], "location");
        Validator::positive_number($ticket["fine_amount"], "fine_amount");
        Validator::datetime($ticket["issued_at"], "issued_at");
        Validator::not_in_future($ticket["issued_at"], "issued_at");
        Validator::one_of($ticket["status"], "status", self::STATUSES);

        if ($this->citizen_dao->get_by_id($ticket["citizen_id"]) === null) {
            throw new ValidationException("Citizen {$ticket["citizen_id"]} does not exist.");
        }
        if ($this->violation_dao->get_by_id($ticket["violation_type_id"]) === null) {
            throw new ValidationException("Violation type {$ticket["violation_type_id"]} does not exist.");
        }
        $officer = $this->user_dao->get_by_id($ticket["officer_id"]);
        if ($officer === null) {
            throw new ValidationException("Officer {$ticket["officer_id"]} does not exist.");
        }
        if ($officer["status"] !== "active") {
            throw new ValidationException("Officer {$officer["badge_number"]} is not active yet and cannot issue tickets.");
        }
        if (isset($ticket["vehicle_id"])) {
            Validator::integer($ticket["vehicle_id"], "vehicle_id", 1);
            if ($this->vehicle_dao->get_by_id($ticket["vehicle_id"]) === null) {
                throw new ValidationException("Vehicle {$ticket["vehicle_id"]} does not exist.");
            }
        }
    }

    // TCK-88231 -> TCK-88232
    private function next_ticket_number()
    {
        $last = $this->dao->get_last_ticket_number();
        $number = $last ? (int) substr($last, 4) + 1 : 10001;
        return "TCK-" . $number;
    }
}
