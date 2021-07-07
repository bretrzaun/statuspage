<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\LogfileContentCheck;
use PHPUnit\Framework\TestCase;

class LogfileContentCheckTest extends TestCase
{
    public function testFileDoesNotExists(): void
    {
        $check = new LogfileContentCheck('Test', 'doesnotexist.txt');
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Log file doesnotexist.txt does not exist!', $result->getError());
    }

    public function testSuccess(): void
    {
        $check = new LogfileContentCheck('Test', __DIR__.'/../test.log');
        $check->setCheckfor('complete');
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
    }

    public function testFailure(): void
    {
        $check = new LogfileContentCheck('Test', __DIR__.'/../test.log');
        $check->setCheckfor('foo');
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Log file failure', $result->getError());
        $this->assertStringContainsString('Timestamp:', $result->getDetails());
    }
}
