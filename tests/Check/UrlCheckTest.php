<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\UrlCheck;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class UrlCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $mock = new MockHandler([
            new Response(200)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $check = new UrlCheck('Test-Url', 'http://www.example.org');
        $check->setHttpClient($client);
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $mock = new MockHandler([
            new Response(404)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $check = new UrlCheck('Test', 'http://foo.int');
        $check->setHttpClient($client);
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertContains('URL failed: http://foo.int', $result->getError());
    }
}
