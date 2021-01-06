<?php

namespace Hulotte\Database;

use PDOStatement;

/**
 * Class Table
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @package Hulotte\Database
 */
class Repository
{
    /**
     * @var string
     */
    protected string $entity;

    /**
     * @var string
     */
    protected string $table;

    /**
     * Table constructor
     * @param Database $database
     */
    public function __construct(private Database $database)
    {
    }

    /**
     * Select all entry on the table
     * @return mixed
     */
    public function all(): mixed
    {
        return $this->query('SELECT * FROM ' . $this->table);
    }

    /**
     * Select on entry on the table search by his id
     * @param int $id
     * @return mixed
     */
    public function find(int $id): mixed
    {
        return $this->query(
            'SELECT * FROM ' . $this->table . ' WHERE id = :id',
            [':id' => $id],
            true
        );
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     * @return string
     */
    public function getLastInsertId(): string
    {
        return $this->database->getLastInsertId();
    }

    /**
     * Define witch method call and when to hydrate datas
     * @param string $statement
     * @param array|null $params
     * @param bool $one
     * @return mixed
     */
    public function query(string $statement, ?array $params = null, bool $one = false): mixed
    {
        if ($params) {
            $results = $this->database->prepare($statement, $params, $one);
        } else {
            $results = $this->database->query($statement, $one);
        }

        if ($results instanceof PDOStatement) {
            return $results;
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

    /**
     * @param string $entity
     */
    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * Hydrate an object
     * @param array $results
     * @return mixed
     */
    private function hydrate(array $results): mixed
    {
        $entity = new $this->entity();

        foreach ($results as $param => $value) {
            $methodName = 'set' . ucfirst($param);
            $entity->$methodName($value);
        }

        return $entity;
    }
}
