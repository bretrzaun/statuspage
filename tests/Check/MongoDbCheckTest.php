<?php
namespace BretRZaun\StatusPage\Tests\Check;

use MongoDB\Client;
use BretRZaun\StatusPage\Check\MongoDbCheck;
use PHPUnit\Framework\TestCase;
use MongoDB\Model\DatabaseInfo;
use MongoDB\Model\CollectionInfo;

class MongoDbCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('listDatabases')
            ->willReturn(new \ArrayIterator(
                [new DatabaseInfo(['name' => 'test'])]
            ));

        $check = new MongoDbCheck('mongodb test', $client);
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('listDatabases')
            ->willThrowException(new \MongoDB\Driver\Exception\ConnectionTimeoutException);

        $check = new MongoDbCheck('mongodb test', $client);
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
    }

    public function testDatabaseExists(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->exactly(2))
            ->method('listDatabases')
            ->willReturn(new \ArrayIterator(
                [new DatabaseInfo(['name' => 'test'])]
            ));

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseExists('test');
        $result = $check->checkStatus();
        $this->assertTrue($result->isSuccess());

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseExists('foo');
        $result = $check->checkStatus();
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Database foo does not exist', $result->getError());
    }

    public function testCollectionExists(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->exactly(2))
            ->method('listDatabases')
            ->willReturn(new \ArrayIterator(
                [new DatabaseInfo(['name' => 'test-db'])]
            ));

        $mockDatabase = $this->createMock(\MongoDB\Database::class);
        $mockDatabase->expects($this->exactly(2))
            ->method('listCollections')
            ->willReturn(new \ArrayIterator(
                [new CollectionInfo(['name' => 'my-collection'])]
            ));

        $client->expects($this->exactly(2))
            ->method('selectDatabase')
            ->willReturn($mockDatabase)
            ;

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseHasCollection('test-db', 'my-collection');
        $result = $check->checkStatus();
        $this->assertEquals('', $result->getError());
        $this->assertTrue($result->isSuccess());

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseHasCollection('test-db', 'foo');
        $result = $check->checkStatus();
        $this->assertFalse($result->isSuccess());
        $this->assertEquals(
            'Collection foo does not exist in database test-db',
            $result->getError()
        );
    }
}