<?php
require_once __DIR__ . "/../../config/config.php";

/*
 * BaseDao: the parent of every DAO.
 * - one shared PDO connection for all DAOs
 * - generic CRUD built from arrays, with a whitelist of columns
 * - every query is a prepared statement
 */
class BaseDao
{
    private static $connection = null;   // one connection for all DAOs
    protected $table_name;
    protected $columns;                   // columns that add() and update() may write

    public function __construct($table_name, $columns = [])
    {
        $this->table_name = $table_name;
        $this->columns = $columns;
    }

    public static function connection()
    {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "mysql:host=" . Config::DB_HOST()
                    . ";port=" . Config::DB_PORT()
                    . ";dbname=" . Config::DB_NAME() . ";charset=utf8mb4",
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        return self::$connection;
    }

    // ----- Helpers for child DAOs -----

    // All rows of a SELECT
    protected function query($query, $params = [])
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // First row of a SELECT, or null when there is none
    protected function query_unique($query, $params = [])
    {
        return $this->query($query, $params)[0] ?? null;
    }

    // INSERT, UPDATE or DELETE: returns how many rows changed
    protected function execute($query, $params = [])
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    // Keeps only the keys that are real, writable columns of this table.
    // Column names are glued into the SQL, so unknown keys must never pass.
    protected function only_columns($entity)
    {
        return array_intersect_key($entity, array_flip($this->columns));
    }

    // ----- Public methods for services -----

    public function get_all()
    {
        return $this->query("SELECT * FROM {$this->table_name}");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(
            "SELECT * FROM {$this->table_name} WHERE id = :id",
            ["id" => $id]
        );
    }

    public function count_all()
    {
        return (int) $this->query_unique(
            "SELECT COUNT(*) AS total FROM {$this->table_name}"
        )["total"];
    }

    // Inserts the row and returns it with its new id
    public function add($entity)
    {
        $entity = $this->only_columns($entity);
        if (empty($entity)) {
            throw new InvalidArgumentException("No valid columns to insert into {$this->table_name}");
        }
        $columns = array_keys($entity);

        $query = "INSERT INTO {$this->table_name} ("
               . implode(", ", $columns) . ") VALUES (:"
               . implode(", :", $columns) . ")";
        $this->execute($query, $entity);

        $entity["id"] = (int) self::connection()->lastInsertId();
        return $entity;
    }

    // Changes only the columns that are in $entity; returns rows changed
    public function update($entity, $id)
    {
        $entity = $this->only_columns($entity);
        if (empty($entity)) {
            throw new InvalidArgumentException("No valid columns to update in {$this->table_name}");
        }

        $parts = [];
        foreach (array_keys($entity) as $column) {
            $parts[] = "$column = :$column";
        }
        $query = "UPDATE {$this->table_name} SET " . implode(", ", $parts) . " WHERE id = :id";
        $entity["id"] = $id;
        return $this->execute($query, $entity);
    }

    // Returns rows deleted (1 = deleted, 0 = no row with this id)
    public function delete($id)
    {
        return $this->execute(
            "DELETE FROM {$this->table_name} WHERE id = :id",
            ["id" => $id]
        );
    }

    // ----- Transactions: the service decides what belongs together -----

    public function begin_transaction() { self::connection()->beginTransaction(); }
    public function commit()            { self::connection()->commit(); }
    public function rollback()          { self::connection()->rollBack(); }
}
