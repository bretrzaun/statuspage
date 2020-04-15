<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\LogfileContentCheck;
use PHPUnit\Framework\TestCase;

class LogfileContentCheckTest extends TestCase
{
    public function testFileDoesNotExists(): void
    {
        $check = new LogfileContentCheck('Test', 'doesnotexist.txt');
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('Log file doesnotexist.txt does not exist!', $result->getError());
    }

    public function testSuccess(): void
    {
        $check = new LogfileContentCheck('Test', __DIR__.'/../test.log');
        $check->setCheckfor('complete');
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
    }

    public function testFailure(): void
    {
        $check = new LogfileContentCheck('Test', __DIR__.'/../test.log');
        $check->setCheckfor('foo');
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('Log file failure', $result->getError());
        $this->assertStringContainsString('Timestamp:', $result->getDetails());
    }
}
