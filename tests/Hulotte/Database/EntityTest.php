<?php

namespace tests\Hulotte\Database;

/**
 * Class Entity
 * Use to test hydratation
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @package tests\Hulotte\Database
 */
class EntityTest
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
