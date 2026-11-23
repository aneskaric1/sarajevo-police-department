<?php
require_once __DIR__ . "/BaseService.php";
require_once __DIR__ . "/../dao/UserDao.php";

// Officers and administrators. The client sends "password"; only its hash is stored.
class UserService extends BaseService
{
    public const ROLES = ["admin", "user"];
    public const STATUSES = ["active", "pending"];

    protected $entity_name = "Officer";

    public function __construct()
    {
        parent::__construct(new UserDao());
    }

    public function get_by_status($status)
    {
        Validator::one_of($status, "status", self::STATUSES);
        return $this->dao->get_by_status($status);
    }

    public function add($user)
    {
        $user = $this->normalize($user);
        unset($user["password_hash"]);              // the client may never set the hash itself
        Validator::required($user, ["password"]);
        Validator::length($user["password"], "password", 8, 72);
        $this->validate($user);

        $user["password_hash"] = password_hash($user["password"], PASSWORD_BCRYPT);
        unset($user["password"]);
        $created = $this->dao->add($user);
        return $this->get_by_id($created["id"]);
    }

    public function update($user, $id)
    {
        $user = $this->normalize($user);
        unset($user["password_hash"]);
        if (!empty($user["password"])) {            // a new password is optional on update
            Validator::length($user["password"], "password", 8, 72);
            $user["password_hash"] = password_hash($user["password"], PASSWORD_BCRYPT);
        }
        unset($user["password"]);
        return parent::update($user, $id);
    }

    protected function validate($user, $id = null)
    {
        Validator::required($user, ["badge_number", "first_name", "last_name", "email"]);
        Validator::pattern($user["badge_number"], "badge_number", '/^[A-Z]-[0-9]{3}$/', "P-214");
        Validator::length($user["first_name"], "first_name", 2, 50);
        Validator::length($user["last_name"], "last_name", 2, 50);
        Validator::no_html($user["first_name"], "first_name");
        Validator::no_html($user["last_name"], "last_name");
        Validator::email($user["email"], "email");
        if (isset($user["officer_rank"])) Validator::length($user["officer_rank"], "officer_rank", 2, 50);
        if (isset($user["role"]))         Validator::one_of($user["role"], "role", self::ROLES);
        if (isset($user["status"]))       Validator::one_of($user["status"], "status", self::STATUSES);

        $same_email = $this->dao->get_by_email($user["email"]);
        if ($same_email && $same_email["id"] != $id) {
            throw new ValidationException("An officer with the e-mail '{$user["email"]}' already exists.");
        }
        $same_badge = $this->dao->get_by_badge_number($user["badge_number"]);
        if ($same_badge && $same_badge["id"] != $id) {
            throw new ValidationException("Badge number '{$user["badge_number"]}' is already taken.");
        }
    }
}
