<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\ElasticsearchCheck;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use PHPUnit\Framework\TestCase;

class ElasticsearchCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('ping')
            ->willReturn(true);

        $check = new ElasticsearchCheck('elasticsearch test', $client);
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('ping')
            ->willReturn(false);

        $check = new ElasticsearchCheck('elasticsearch test', $client);
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
    }

    public function testMissingIndex(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('ping')
            ->willReturn(true);

        $incidesMock = $this->createMock(IndicesNamespace::class);
        $incidesMock->expects($this->once())
            ->method('exists')
            ->with(['index' => 'notexisting-test-index'])
            ->willReturn(false);

        $client->expects($this->once())
            ->method('indices')
            ->willReturn($incidesMock);

        $check = new ElasticsearchCheck('elasticsearch test', $client, ['notexisting-test-index']);
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Index \'notexisting-test-index\' does not exist', $result->getError());
    }
}
