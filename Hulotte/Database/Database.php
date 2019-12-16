<?php

namespace Hulotte\Database;

use PDO;
use PDOStatement;

/**
 * Class Database
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @package Hulotte\Database
 */
class Database
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Database constructor
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Launch a prepare request
     * @param string $statement
     * @param array $params
     * @param bool $one
     * @return array|mixed|PDOStatement
     */
    public function prepare(string $statement, array $params, bool $one = false)
    {
        $query = $this->pdo->prepare($statement);
        $query->execute($params);

        return $this->launch($statement, $query, $one);
    }

    /**
     * Launch a query
     * @param string $statement
     * @param bool $one
     * @return array|mixed|PDOStatement
     */
    public function query(string $statement, bool $one = false)
    {
        $query = $this->pdo->query($statement);

        return $this->launch($statement, $query, $one);
    }

    /**
     * Define the type of return
     * @param string $statement
     * @param PDOStatement $query
     * @param bool $one
     * @return array|mixed|PDOStatement
     */
    private function launch(string $statement, PDOStatement $query, bool $one)
    {
        if (strpos($statement, 'UPDATE') === 0
            || strpos($statement, 'INSERT') === 0
            || strpos($statement, 'DELETE') === 0
            || strpos($statement, 'CREATE') === 0
        ) {
            return $query;
        }

        if ($one) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }
}
