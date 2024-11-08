<?php
namespace BretRZaun\StatusPage\Tests\Check;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use BretRZaun\StatusPage\Check\ElasticsearchCheck;
use DG\BypassFinals;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Endpoints\Indices;
use Elastic\Elasticsearch\Response\Elasticsearch;
use PHPUnit\Framework\TestCase;

// enable mock final classes
#[RunTestsInSeparateProcesses]
class ElasticsearchCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        BypassFinals::enable();

        /** @noinspection PhpUnitInvalidMockingEntityInspection */
        $client = $this->createMock(Client::class);
        $ping = $this->createMock(Elasticsearch::class);
        $ping->expects($this->once())
            ->method('asBool')
            ->willReturn(true);
        $client->expects($this->any())
            ->method('ping')
            ->willReturn($ping);

        $check = new ElasticsearchCheck('elasticsearch test', $client);
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        BypassFinals::enable();

        /** @noinspection PhpUnitInvalidMockingEntityInspection */
        $client = $this->createMock(Client::class);
        $ping = $this->createMock(Elasticsearch::class);
        $ping->expects($this->once())
            ->method('asBool')
            ->willReturn(false);
        $client->expects($this->any())
            ->method('ping')
            ->willReturn($ping);

        $check = new ElasticsearchCheck('elasticsearch test', $client);
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
    }

    public function testMissingIndex(): void
    {
        BypassFinals::enable();

        /** @noinspection PhpUnitInvalidMockingEntityInspection */
        $client = $this->createMock(Client::class);
        $ping = $this->createMock(Elasticsearch::class);
        $ping->expects($this->once())
            ->method('asBool')
            ->willReturn(true);
        $client->expects($this->any())
            ->method('ping')
            ->willReturn($ping);

        $exists = $this->createMock(Elasticsearch::class);
        $exists->expects($this->once())
            ->method('asBool')
            ->willReturn(false);
        $incidesMock = $this->createMock(Indices::class);
        $incidesMock->expects($this->once())
            ->method('exists')
            ->with(['index' => 'notexisting-test-index'])
            ->willReturn($exists);

        $client->expects($this->once())
            ->method('indices')
            ->willReturn($incidesMock);

        $check = new ElasticsearchCheck('elasticsearch test', $client, ['notexisting-test-index']);
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Index \'notexisting-test-index\' does not exist', $result->getError());
    }
}
