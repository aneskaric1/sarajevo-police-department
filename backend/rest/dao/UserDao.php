<?php
require_once __DIR__ . "/BaseDao.php";

// users: officers and administrators.
// The password hash is never returned, except by get_by_email() which login needs.
class UserDao extends BaseDao
{
    private const PUBLIC_COLUMNS =
        "id, badge_number, first_name, last_name, email, officer_rank, role, status, created_at";

    public function __construct()
    {
        parent::__construct("users", [
            "badge_number", "first_name", "last_name", "email",
            "password_hash", "officer_rank", "role", "status",
        ]);
    }

    public function get_all()
    {
        return $this->query("SELECT " . self::PUBLIC_COLUMNS . " FROM users ORDER BY last_name, first_name");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(
            "SELECT " . self::PUBLIC_COLUMNS . " FROM users WHERE id = :id",
            ["id" => $id]
        );
    }

    // add() from BaseDao returns what was inserted; remove the hash from it
    public function add($entity)
    {
        $user = parent::add($entity);
        unset($user["password_hash"]);
        return $user;
    }

    // For login (Milestone 4): the only method that returns password_hash
    public function get_by_email($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", ["email" => $email]);
    }

    public function get_by_badge_number($badge_number)
    {
        return $this->query_unique(
            "SELECT " . self::PUBLIC_COLUMNS . " FROM users WHERE badge_number = :badge",
            ["badge" => $badge_number]
        );
    }

    // e.g. "pending" accounts that an admin still has to approve
    public function get_by_status($status)
    {
        return $this->query(
            "SELECT " . self::PUBLIC_COLUMNS . " FROM users WHERE status = :status ORDER BY created_at",
            ["status" => $status]
        );
    }
}
