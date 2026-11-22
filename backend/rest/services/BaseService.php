<?php
require_once __DIR__ . "/../../utils/exceptions.php";
require_once __DIR__ . "/../../utils/Validator.php";

/*
 * BaseService: the parent of every service.
 * - talks to its DAO, never to HTTP (no $_POST, no echo, no status codes)
 * - add() and update() always call validate(), so no rule can be skipped
 * - a missing record becomes NotFoundException (404)
 */
class BaseService
{
    protected $dao;
    protected $entity_name = "Record";   // used in messages: "Ticket not found."

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function get_all()
    {
        return $this->dao->get_all();
    }

    public function get_by_id($id)
    {
        $entity = $this->dao->get_by_id($id);
        if ($entity === null) {
            throw new NotFoundException("{$this->entity_name} not found.");
        }
        return $entity;
    }

    public function add($entity)
    {
        $entity = $this->normalize($entity);
        $this->validate($entity);
        $created = $this->dao->add($entity);
        return $this->get_by_id($created["id"]);   // read back, with JOINed names
    }

    // PUT may send only the fields that change. They are merged with the
    // saved record and the RESULT is validated, so a record can never
    // end up in a state that add() would refuse.
    public function update($entity, $id)
    {
        $existing = $this->get_by_id($id);
        $merged = array_merge($existing, $this->normalize($entity));
        $this->validate($merged, $id);
        $this->dao->update($merged, $id);          // BaseDao keeps only real columns
        return $this->get_by_id($id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);                     // 404 if it does not exist
        $this->dao->delete($id);
    }

    // Child services write their rules here. $id is null for add().
    protected function validate($entity, $id = null)
    {
    }

    // Trim text and turn "" into null, so optional fields are stored as NULL
    protected function normalize($entity)
    {
        if (!is_array($entity)) {
            throw new ValidationException("The request body must be a JSON object.");
        }
        foreach ($entity as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
                $entity[$key] = $value === "" ? null : $value;
            }
        }
        return $entity;
    }

    // HTML forms send "2026-11-20T18:00"; the database wants "2026-11-20 18:00:00"
    protected function normalize_datetime($value)
    {
        if ($value === null) return null;
        $value = str_replace("T", " ", $value);
        return strlen($value) === 16 ? $value . ":00" : $value;
    }
}
