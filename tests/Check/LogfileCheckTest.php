<?php

use BretRZaun\StatusPage\Check\LogfileCheck;

class LogfileCheckTest extends PHPUnit_Framework_TestCase
{
    public function testFileDoesNotExists()
    {
        $check = new LogfileCheck('Test', 'doesnotexist.txt');
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('Log file doesnotexist.txt does not exist!', $result->getError());
    }

    public function testSuccess()
    {
        $check = new LogfileCheck('Test', __DIR__.'/../test.log');
        $check->setCheckfor('complete');
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
    }

    public function testFailure()
    {
        $check = new LogfileCheck('Test', __DIR__.'/../test.log');
        $check->setCheckfor('foo');
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('Log file failure', $result->getError());
        $this->assertContains('Timestamp:', $result->getDetails());
    }
}
