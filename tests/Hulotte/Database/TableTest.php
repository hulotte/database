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
     * @var Table
     */
    private $table;

    /**
     * @test
     * @covers \Hulotte\Database\Table::all
     */
    public function all(): void
    {
        $this->database->expects($this->once())->method('query')
            ->willReturn([['id' => 1, 'name' => 'Sébastien'], ['id' => 2, 'name' => 'Elodie']]);
        $this->getTable()->setEntity(Entity::class);
        $this->getTable()->setTable('test');
        $results = $this->getTable()->all();

        $this->assertIsArray($results);
        $this->assertInstanceOf(Entity::class, $results[0]);
        $this->assertSame('Sébastien', $results[0]->getName());
        $this->assertSame('Elodie', $results[1]->getName());
    }

    /**
     * @covers \Hulotte\Database\Table::find
     * @test
     */
    public function find(): void
    {
        $this->database->expects($this->once())->method('prepare')
            ->willReturn(['id' => 1, 'name' => 'Sébastien']);
        $this->getTable()->setEntity(Entity::class);
        $this->getTable()->setTable('test');
        $result = $this->getTable()->find(1);

        $this->assertInstanceOf(Entity::class, $result);
        $this->assertSame('Sébastien', $result->getName());
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
        if (!$this->table) {
            $this->table = new Table($this->database);
        }

        return $this->table;
    }
}
