<?php

namespace tests\Hulotte\Database;

use PDO;
use PDOStatement;
use Hulotte\Database\Database;
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
    private PDO $pdo;

    /**
     * @var PDOStatement
     */
    private PDOStatement $pdoStatement;

    /**
     * @covers \Hulotte\Database\Database::query
     * @test
     */
    public function query(): void
    {
        $this->pdoStatement->expects($this->once())
            ->method('fetch')
            ->willReturn([]);
        $this->pdo->expects($this->once())
            ->method('query')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $results = $database->query('SELECT * FROM table WHERE id = 1', true);

        $this->assertIsArray($results);
    }

    /**
     * @covers \Hulotte\Database\Database::query
     * @test
     */
    public function queryWithFetchAll(): void
    {
        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);
        $this->pdo->expects($this->once())
            ->method('query')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $results = $database->query('SELECT * FROM table');

        $this->assertIsArray($results);
    }

    /**
     * @covers \Hulotte\Database\Database::query
     * @dataProvider queryProvider
     * @param string $statement
     * @test
     */
    public function queryNotWithSelect(string $statement): void
    {
        $this->pdo->expects($this->once())
            ->method('query')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $result = $database->query($statement);

        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    /**
     * @covers \Hulotte\Database\Database::query
     * @test
     */
    public function queryNotWithSelectMin(): void
    {
        $this->pdo->expects($this->once())
            ->method('query')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $result = $database->query('insert into tests (id, name) values (1, "Riri"))');

        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    /**
     * @covers \Hulotte\Database\Database::prepare
     * @test
     */
    public function prepare(): void
    {
        $this->pdoStatement->expects($this->once())
            ->method('execute');
        $this->pdoStatement->expects($this->once())
            ->method('fetch')
            ->willReturn([]);
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $results = $database->prepare('SELECT * FROM table WHERE id = :id', [':id' => 1], true);

        $this->assertIsArray($results);
    }

    /**
     * @covers \Hulotte\Database\Database::prepare
     * @test
     */
    public function prepareFetchAll(): void
    {
        $this->pdoStatement->expects($this->once())
            ->method('execute');
        $this->pdoStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $results = $database->prepare('SELECT * FROM table WHERE id = :id', [':id' => 1]);

        $this->assertIsArray($results);
    }

    /**
     * @covers \Hulotte\Database\Database::prepare
     * @dataProvider prepareProvider
     * @param string $statement
     * @param array $params
     * @test
     */
    public function prepareNotWithSelect(string $statement, array $params): void
    {
        $this->pdoStatement->expects($this->once())
            ->method('execute');
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStatement);

        $database = new Database($this->pdo);
        $result = $database->prepare($statement, $params);

        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    /**
     * @covers \Hulotte\Database\Database::getLastInsertId
     * @test
     */
    public function getLastInsertId(): void
    {
        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('');
        $database = new Database($this->pdo);
        $result = $database->getLastInsertId();

        $this->assertIsString($result);
    }

    /**
     * Data providers for query tests
     * @return string[][]
     */
    public function queryProvider(): array
    {
        return [
            ['statement' => 'INSERT INTO tests (id, name) VALUES (1, "Riri"))'],
            ['statement' => 'UPDATE test SET name = "Fifi" WHERE id = 1)'],
            ['statement' => 'DELETE test WHERE id = 1'],
            ['statement' => 'CREATE TABLE utilisateur (id INT PRIMARY KEY NOT NULL, nom VARCHAR(100))'],
        ];
    }

    /**
     * Data providers for prepare tests
     * @return array[]
     */
    public function prepareProvider(): array
    {
        return [
            [
                'statement' => 'INSERT INTO test (id, name) VALUES (:id, :name)',
                'params' => [':id' => 1, ':name' => 'Riri']
            ],
            ['statement' => 'UPDATE test SET name = :name WHERE id = :id', 'params' => [':id' => 2, ':name' => 'Fifi']],
            ['statement' => 'DELETE test WHERE id = :id', 'params' => [':id' => 1]],
        ];
    }

    protected function setUp(): void
    {
        $this->pdoStatement = $this->createMock(PDOStatement::class);
        $this->pdo = $this->createMock(PDO::class);
    }
}
