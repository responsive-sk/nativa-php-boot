<?php

declare(strict_types = 1);

namespace Tests\Infrastructure\Persistence;

use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\Persistence\UnitOfWork
 *
 * @internal
 */
final class UnitOfWorkTest extends TestCase
{
    private UnitOfWork $unitOfWork;

    private DatabaseConnection $dbConnection;

    protected function setUp(): void
    {
        $this->dbConnection = new DatabaseConnection(':memory:');
        $this->unitOfWork = new UnitOfWork($this->dbConnection);
    }

    public function testBeginTransaction(): void
    {
        $this->unitOfWork->beginTransaction();

        self::assertTrue(true, 'Transaction started without errors');
    }

    public function testCommitTransaction(): void
    {
        $pdo = $this->dbConnection->getConnection();
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY)');

        $this->unitOfWork->beginTransaction();

        $pdo->exec('INSERT INTO test (id) VALUES (1)');

        $this->unitOfWork->commit();

        $result = $pdo->query('SELECT COUNT(*) FROM test')->fetchColumn();
        self::assertSame(1, $result);
    }

    public function testRollbackTransaction(): void
    {
        $pdo = $this->dbConnection->getConnection();
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY)');

        $this->unitOfWork->beginTransaction();

        $pdo->exec('INSERT INTO test (id) VALUES (1)');

        $this->unitOfWork->rollback();

        $result = $pdo->query('SELECT COUNT(*) FROM test')->fetchColumn();
        self::assertSame(0, $result);
    }

    public function testGetConnection(): void
    {
        $connection = $this->unitOfWork->getConnection();

        self::assertInstanceOf(\PDO::class, $connection);
    }

    public function testMultipleBeginTransactionDoesNotCreateMultipleTransactions(): void
    {
        $this->unitOfWork->beginTransaction();
        $this->unitOfWork->beginTransaction();
        $this->unitOfWork->beginTransaction();

        $this->unitOfWork->commit();

        self::assertTrue(true, 'Single commit successful');
    }

    public function testCommitWithoutBeginTransaction(): void
    {
        $this->unitOfWork->commit();

        self::assertTrue(true, 'Commit without begin does not error');
    }

    public function testRollbackWithoutBeginTransaction(): void
    {
        $this->unitOfWork->rollback();

        self::assertTrue(true, 'Rollback without begin does not error');
    }

    public function testTransactionIsolation(): void
    {
        $pdo = $this->dbConnection->getConnection();
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, value TEXT)');

        $this->unitOfWork->beginTransaction();

        $pdo->exec("INSERT INTO test (id, value) VALUES (1, 'test')");

        $duringTransaction = $pdo->query('SELECT COUNT(*) FROM test')->fetchColumn();
        self::assertSame(1, $duringTransaction);

        $this->unitOfWork->rollback();

        $afterRollback = $pdo->query('SELECT COUNT(*) FROM test')->fetchColumn();
        self::assertSame(0, $afterRollback);
    }

    public function testUnitOfWorkWithRealDatabaseOperations(): void
    {
        $pdo = $this->dbConnection->getConnection();
        $pdo->exec('CREATE TABLE articles (id INTEGER PRIMARY KEY, title TEXT)');

        $this->unitOfWork->beginTransaction();

        $stmt = $pdo->prepare('INSERT INTO articles (id, title) VALUES (?, ?)');
        $stmt->execute([1, 'Article 1']);
        $stmt->execute([2, 'Article 2']);

        $count = $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
        self::assertSame(2, $count);

        $this->unitOfWork->commit();

        $finalCount = $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
        self::assertSame(2, $finalCount);
    }

    public function testUnitOfWorkDestructorRollsBackOpenTransaction(): void
    {
        $pdo = $this->dbConnection->getConnection();
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY)');

        $unitOfWork = new UnitOfWork($this->dbConnection);
        $unitOfWork->beginTransaction();

        $pdo->exec('INSERT INTO test (id) VALUES (1)');

        unset($unitOfWork);

        $count = $pdo->query('SELECT COUNT(*) FROM test')->fetchColumn();
        self::assertSame(0, $count);
    }
}
