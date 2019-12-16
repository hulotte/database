<?php

namespace tests\Hulotte\Database;

use Hulotte\Database\{
    Database,
    Table
};
use PHPUnit\Framework\TestCase;

/**
 * Class TableTest
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @covers \Hulotte\Database\Table
 * @package tests\Hulotte\Database
 */
class TableTest extends TestCase
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @test
     * @covers \Hulotte\Database\Table::all
     */
    public function all(): void
    {
        $this->database->expects($this->once())->method('query');
        $this->getTable()->all();
    }

    /**
     * @covers \Hulotte\Database\Table::find
     * @test
     */
    public function find(): void
    {
        $this->database->expects($this->once())->method('prepare');
        $this->getTable()->find(1);
    }

    /**
     * Define Database mock
     */
    protected function setUp(): void
    {
        $this->database = $this->createMock(Database::class);
    }

    /**
     * @return Table
     */
    private function getTable(): Table
    {
        return new Table($this->database);
    }
}
