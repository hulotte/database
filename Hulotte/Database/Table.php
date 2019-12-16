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
    protected $entity;

    /**
     * @var string
     */
    protected $table;

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
        return $this->query('SELECT * FROM ' . $this->table);
    }

    /**
     * Select on entry on the table search by his id
     * @param int $id
     * @return array|mixed|PDOStatement
     */
    public function find(int $id)
    {
        return $this->query(
            'SELECT * FROM ' . $this->table . ' WHERE id = :id',
            [':id' => $id],
            true
        );
    }

    /**
     * @param string $entity
     */
    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Hydrate an object
     * @param array $results
     * @return mixed
     */
    private function hydrate(array $results)
    {
        $entity = new $this->entity();

        foreach ($results as $param => $value) {
            $methodName = 'set' . ucfirst($param);
            $entity->$methodName($value);
        }

        return $entity;
    }

    /**
     * Define witch method call and when to hydrate datas
     * @param string $statement
     * @param array|null $params
     * @param bool $one
     * @return array|mixed
     */
    private function query(string $statement, ?array $params = null, bool $one = false)
    {
        if ($params) {
            $results = $this->database->prepare($statement, $params, $one);
        } else {
            $results = $this->database->query($statement, $one);
        }

        if ($one) {
            return $this->hydrate($results);
        }

        $entities = [];

        foreach ($results as $result) {
            $entities[] = $this->hydrate($result);
        }

        return $entities;
    }
}
