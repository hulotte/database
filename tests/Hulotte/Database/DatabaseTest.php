<?php

namespace tests\Hulotte\Database;

use Hulotte\Database\Database;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Class DatabaseTests
 * @author Sébastien CLEMENT<s.clement@la-taniere.net>
 * @covers \Hulotte\Database\Database
 * @package tests\Hulotte\Database
 */
class DatabaseTest extends TestCase
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var PDOStatement
     */
    private $pdoStatement;

    /**
     * @covers \Hulotte\Database\Database::query
     * @test
     */
    public function querySimple(): void
    {
        $this->pdoStatement->expects($this->once())->method('fetch');
        $this->pdo->expects($this->once())->method('query')
            ->willReturn($this->pdoStatement);

        $this->getDatabase()->query('SELECT * FROM table WHERE id = 1', true);
    }

    /**
     * @covers \Hulotte\Database\Database::query
     * @test
     */
    public function queryWithFetchAll(): void
    {
        $this->pdoStatement->expects($this->once())->method('fetchAll');
        $this->pdo->expects($this->once())->method('query')
            ->willReturn($this->pdoStatement);

        $this->getDatabase()->query('SELECT * FROM table');
    }

    /**
     * @covers \Hulotte\Database\Database::query
     * @test
     */
    public function queryWithUpdateInsertDeleteAndCreate(): void
    {
        $this->pdo->expects($this->exactly(4))
            ->method('query')
            ->willReturn($this->pdoStatement);

        $database = $this->getDatabase();
        $resultInsert = $database->query('INSERT INTO test (id, name) VALUES (1, "Riri")');
        $resultUpdate = $database->query('UPDATE test SET name = "Fifi" WHERE id = 1');
        $resultDelete = $database->query('DELETE test WHERE id = 1');
        $resultCreate = $database->query('CREATE TABLE utilisateur (id INT PRIMARY KEY NOT NULL, nom VARCHAR(100))');

        $this->assertInstanceOf(PDOStatement::class, $resultInsert);
        $this->assertInstanceOf(PDOStatement::class, $resultUpdate);
        $this->assertInstanceOf(PDOStatement::class, $resultDelete);
        $this->assertInstanceOf(PDOStatement::class, $resultCreate);
    }

    /**
     * @covers \Santa\Database\Database::prepare
     * @test
     */
    public function prepareSimple()
    {
        $this->pdoStatement->expects($this->once())->method('execute');
        $this->pdoStatement->expects($this->once())->method('fetch');
        $this->pdo->expects($this->once())->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->getDatabase()->prepare('SELECT * FROM table WHERE id = :id', [':id' => 1], true);
    }

    /**
     * @covers \Santa\Database\Database::prepare
     * @test
     */
    public function prepareFetchAll()
    {
        $this->pdoStatement->expects($this->once())->method('execute');
        $this->pdoStatement->expects($this->once())->method('fetchAll');
        $this->pdo->expects($this->once())->method('prepare')
            ->willReturn($this->pdoStatement);

        $this->getDatabase()->prepare('SELECT * FROM table WHERE id = :id', [':id' => 1]);
    }

    /**
     * @covers \Santa\Database\Database::prepare
     * @test
     */
    public function prepareWithUpdateInsertDelete()
    {
        $this->pdoStatement->expects($this->exactly(3))
            ->method('execute');
        $this->pdo->expects($this->exactly(3))
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $database = $this->getDatabase();
        $resultInsert = $database->prepare(
            'INSERT INTO test (id, name) VALUES (:id, :name)',
            [':id' => 1, ':name' => 'Sébastien']
        );
        $resultUpdate = $database->prepare(
            'UPDATE test SET name = :name WHERE id = :id',
            [':id' => 1, ':name' => 'Sébastien']
        );
        $resultDelete = $database->prepare(
            'DELETE test WHERE id = :id',
            [':id' => 1]
        );

        $this->assertInstanceOf(PDOStatement::class, $resultInsert);
        $this->assertInstanceOf(PDOStatement::class, $resultUpdate);
        $this->assertInstanceOf(PDOStatement::class, $resultDelete);
    }

    /**
     * Initialize PDO and PDOStatement mock
     */
    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
    }

    /**
     * Initialize Database object with pdo mock
     * @return Database
     */
    private function getDatabase(): Database
    {
        return new Database($this->pdo);
    }
}
