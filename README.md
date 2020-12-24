# Hulotte Database
## Description

Hulotte Database is an object-relational mapping library.

## Installation

The easiest way to install Hulotte Database is to use
[Composer](https://getcomposer.org/) with this command :

```bash
$ composer require hulotte/database
```

## Description

Hulotte Database is made up of two classes: Database and Repository.

Database is the class that serves as a link with the database. She needs a
PDO instance.

Repository serves as parent class for all classes that will serve as repository
of table. It needs an instance of Database.

## How to use Hulotte Database : the simple method

As we see in description we need to instantiate PDO and passing it to Database

```php
$pdo = new \PDO(
    'mysql:host=<database_host>;dbname=<database_name>', 
    '<username>', 
    '<password>'
);

$database = new \Hulotte\Database\Database($pdo);
```

### Examples of use

```php
// Launch a "fetchAll" query
$results = $database->query('SELECT * FROM user WHERE id = 1');

// Launch a "fetchAll" query with prepare
$results = $database->prepare('SELECT * FROM user WHERE id = :id', [':id' => 1]);

// You can use same methods to launch a simple fetch by passing "true" to the last argument
$results = $database->query('SELECT * FROM user WHERE id = 1', null, true);
$results = $database->prepare('SELECT * FROM user WHERE id = :id', [':id' => 1], null, true)

// The requests which are not in "select" return an instance of PDOStatement
$result = $database->query('UPDATE test SET name = "Fifi" WHERE id = 1)');

// PDO's lastInsertId method is also accessible
$result = $database->getLastInsertId();
```

## How to use Hulotte Database : the repository method

For the example, let's imagine that a User table exists in the database.

First we need to create an entity with setters and getters :

```php
class UserEntity
{
    private int $id;
    
    private string $name;
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

Now we can create the repository for the user table. This repository must extends the 
Hulotte\Database\Repository class. The repository class need an instance of Database class.

```php
class UserRepository extends \Hulotte\Database\Repository
{
    // Define the Entity class
    protected string $entity = UserEntity::class;
    
    // Define the table's name in database
    protected string $table = 'user';
}

$userRepository = new UserRepository($database);
```

### Examples of use

the class Repository has several dynamics methods :

```php
// Get on user by is id
$result = $userRepository->find(1); // Return a UserEntity

// Get all the users
$results = $userRepository->all(); // Return an array of UserEntity

// Get the last insert id
$result = $userRepository->getLastInsertId();
```

If you need to send your specifics queries, you can use the Repository query() method.
The behavior is the same as the Database class except that the query and prepare 
methods are combined in a single method.

```php
// On the UserRepository class
public function findByName(string $name): userEntity
{
    $statement = 'SELECT * FROM ' . $this->table . ' WHERE name = :name';
    
    return $this->query(
        $statement, // Your query 
        [':name' => $name], // Send params call a prepared request
        true // Send true if you want only one result. Send nothing if you want many results
    );
}
```
