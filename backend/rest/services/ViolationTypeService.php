<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/ViolationTypeDao.php";

class ViolationTypeService extends BaseService
{
    protected $entity_name = "Violation type";

    public function __construct()
    {
        parent::__construct(new ViolationTypeDao());
    }

    // Tickets point to their violation type, so a type in use stays
    public function delete($id)
    {
        $this->get_by_id($id);
        $tickets = $this->dao->count_tickets($id);
        if ($tickets > 0) {
            throw new ValidationException("This violation type is used by $tickets ticket(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }

    protected function normalize($type)
    {
        $type = parent::normalize($type);
        if (isset($type["code"])) $type["code"] = strtoupper($type["code"]);
        return $type;
    }

    protected function validate($type, $id = null)
    {
        Validator::required($type, ["code", "name", "standard_fine"]);
        Validator::pattern($type["code"], "code", '/^V-[0-9]{2}$/', "V-07");
        Validator::length($type["name"], "name", 3, 80);
        Validator::no_html($type["name"], "name");
        Validator::positive_number($type["standard_fine"], "standard_fine");
        if ((float) $type["standard_fine"] > 5000) {
            throw new ValidationException("Field 'standard_fine' cannot be more than 5000.");
        }
        if (isset($type["description"])) {
            Validator::length($type["description"], "description", 0, 255);
            Validator::no_html($type["description"], "description");
        }

        $same = $this->dao->get_by_code($type["code"]);
        if ($same && $same["id"] != $id) {
            throw new ValidationException("A violation type with the code '{$type["code"]}' already exists.");
        }
    }
}
