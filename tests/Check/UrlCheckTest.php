<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\UrlCheck;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Psr18Client;

class UrlCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $responses = [
            new MockResponse('body1', [])
        ];
        $client = new Psr18Client(new MockHttpClient($responses));

        $check = new UrlCheck('Test-Url', 'http://www.example.org', $client);
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $responses = [
            new MockResponse('body1', ['http_code' => 404])
        ];
        $client = new Psr18Client(new MockHttpClient($responses));

        $check = new UrlCheck('Test', 'http://foo.int', $client);
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString(
            'HTTP status code for http://foo.int is 404',
            $result->getError()
        );
    }
}
