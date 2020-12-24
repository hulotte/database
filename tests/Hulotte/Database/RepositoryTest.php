<?php

namespace tests\Hulotte\Database;

use Hulotte\Database\Database;
use Hulotte\Database\Repository;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Class TableTest
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @covers \Hulotte\Database\Repository
 * @package tests\Hulotte\Database
 */
class RepositoryTest extends TestCase
{
    /**
     * @var Database
     */
    private Database $database;

    /**
     * @covers \Hulotte\Database\Repository::query
     * @test
     */
    public function queryWithAll(): void
    {
        $this->database->expects($this->once())
            ->method('query')
            ->willReturn([]);
        $repository = new Repository($this->database);

        $results = $repository->query('SELECT * FROM table');

        $this->assertIsArray($results);
    }

    /**
     * @covers \Hulotte\Database\Repository::query
     * @test
     */
    public function queryWithOne(): void
    {
        $this->database->expects($this->once())
            ->method('query')
            ->willReturn(['id' => 1, 'name' => 'Riri']);
        $repository = new Repository($this->database);
        $repository->setEntity(EntityTest::class);

        $result = $repository->query('SELECT * FROM table WHERE id = 1', null, true);

        $this->assertInstanceOf(EntityTest::class, $result);
        $this->assertSame('Riri', $result->getName());
    }

    /**
     * @covers \Hulotte\Database\Repository::query
     * @test
     */
    public function queryWithPrepare(): void
    {
        $this->database->expects($this->once())
            ->method('prepare')
            ->willReturn(['id' => 1, 'name' => 'Riri']);
        $repository = new Repository($this->database);
        $repository->setEntity(EntityTest::class);

        $result = $repository->query('SELECT * FROM table WHERE id = :id', [':id' => 1], true);

        $this->assertInstanceOf(EntityTest::class, $result);
        $this->assertSame('Riri', $result->getName());
    }

    /**
     * @covers \Hulotte\Database\Repository::query
     * @test
     */
    public function queryMany(): void
    {
        $this->database->expects($this->once())
            ->method('query')
            ->willReturn([['id' => 1, 'name' => 'Riri'], ['id' => 2, 'name' => 'Fifi']]);
        $repository = new Repository($this->database);
        $repository->setEntity(EntityTest::class);

        $results = $repository->query('SELECT * FROM table');

        $this->assertIsArray($results);

        foreach ($results as $entity) {
            $this->assertInstanceOf(EntityTest::class, $entity);
        }
    }

    /**
     * @covers \Hulotte\Database\Repository::query
     * @test
     */
    public function queryNotSelect(): void
    {
        $this->database->expects($this->once())
            ->method('query')
            ->willReturn($this->createMock(PDOStatement::class));
        $repository = new Repository($this->database);

        $result = $repository->query('UPDATE table SET name = "Fifi" WHERE id = 1)');

        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    /**
     * @covers \Hulotte\Database\Repository::getLastInsertId
     * @test
     */
    public function getLastInsertId(): void
    {
        $this->database->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('1');
        $repository = new Repository($this->database);

        $result = $repository->getLastInsertId();

        $this->assertIsString($result);
    }

    protected function setUp(): void
    {
        $this->database = $this->createMock(Database::class);
    }
}
