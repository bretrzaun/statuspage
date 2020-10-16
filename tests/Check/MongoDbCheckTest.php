<?php
namespace BretRZaun\StatusPage\Tests\Check;

use MongoDB\Client;
use BretRZaun\StatusPage\Check\MongoDbCheck;
use PHPUnit\Framework\TestCase;

class MongoDbCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('listDatabaseNames')
            ->willReturn(new \ArrayIterator(['test']));

        $check = new MongoDbCheck('mongodb test', $client);
        $result = $check->checkStatus();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('listDatabaseNames')
            ->willThrowException(new \MongoDB\Driver\Exception\ConnectionTimeoutException);

        $check = new MongoDbCheck('mongodb test', $client);
        $result = $check->checkStatus();

        $this->assertFalse($result->getSuccess());
    }

    public function testDatabaseExists(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->exactly(2))
            ->method('listDatabaseNames')
            ->willReturn(new \ArrayIterator(['test']));

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseExists('test');
        $result = $check->checkStatus();
        $this->assertTrue($result->getSuccess());

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseExists('foo');
        $result = $check->checkStatus();
        $this->assertFalse($result->getSuccess());
        $this->assertEquals('Database foo does not exist', $result->getError());
    }

    public function testCollectionExists(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->exactly(2))
            ->method('listDatabaseNames')
            ->willReturn(new \ArrayIterator(['test-db']));

        $mockDatabase = $this->createMock(\MongoDB\Database::class);
        $mockDatabase->expects($this->exactly(2))
            ->method('listCollectionNames')
            ->willReturn(new \ArrayIterator(['my-collection']));

        $client->expects($this->exactly(2))
            ->method('selectDatabase')
            ->willReturn($mockDatabase)
            ;

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseHasCollecion('test-db', 'my-collection');
        $result = $check->checkStatus();
        $this->assertEquals('', $result->getError());
        $this->assertTrue($result->getSuccess());

        $check = new MongoDbCheck('mongodb test', $client);
        $check->ensureDatabaseHasCollecion('test-db', 'foo');
        $result = $check->checkStatus();
        $this->assertFalse($result->getSuccess());
        $this->assertEquals(
            'Collection foo does not exist in database test-db',
            $result->getError()
        );
    }
}