<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\ElasticsearchCheck;
use Elasticsearch\Client;
use PHPUnit\Framework\TestCase;

class ElasticsearchCheckTest extends TestCase
{

    public function testSuccess()
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('ping')
            ->willReturn(true);

        $check = new ElasticsearchCheck('elasticsearch test', $client);
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure()
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('ping')
            ->willReturn(false);

        $check = new ElasticsearchCheck('elasticsearch test', $client);
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
    }
}
