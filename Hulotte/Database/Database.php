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
     * Database constructor
     * @param PDO $pdo
     */
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     * @return string
     */
    public function getLastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Launch a prepare request
     * @param string $statement
     * @param array $params
     * @param bool $one
     * @return array|PDOStatement
     */
    public function prepare(string $statement, array $params, bool $one = false): array|PDOStatement
    {
        $query = $this->pdo->prepare($statement);
        $query->execute($params);

        return $this->launch($statement, $query, $one);
    }

    /**
     * Launch a query
     * @param string $statement
     * @param bool $one
     * @return array|PDOStatement
     */
    public function query(string $statement, bool $one = false): array|PDOStatement
    {
        $query = $this->pdo->query($statement);

        return $this->launch($statement, $query, $one);
    }

    /**
     * Define the type of return
     * @param string $statement
     * @param PDOStatement $query
     * @param bool $one
     * @return array|PDOStatement
     */
    private function launch(string $statement, PDOStatement $query, bool $one): array|PDOStatement
    {
        $keyWords = ['UPDATE', 'INSERT', 'DELETE', 'CREATE'];

        foreach ($keyWords as $keyWord) {
            if (str_starts_with(strtoupper($statement), $keyWord)) {
                return $query;
            }
        }

        if ($one) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }
}
