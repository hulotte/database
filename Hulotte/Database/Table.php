<?php

namespace Hulotte\Database;

use PDOStatement;

/**
 * Class Table
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @package Hulotte\Database
 */
class Table
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var Database
     */
    private $database;

    /**
     * Table constructor
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Select all entry on the table
     * @return array|mixed|PDOStatement
     */
    public function all()
    {
        return $this->database->query('SELECT * FROM ' . $this->tableName);
    }

    /**
     * Select on entry on the table search by his id
     * @param int $id
     * @return array|mixed|PDOStatement
     */
    public function find(int $id)
    {
        return $this->database->prepare(
            'SELECT * FROM ' . $this->tableName . ' WHERE id = :id',
            [':id' => $id],
            true
        );
    }
}
