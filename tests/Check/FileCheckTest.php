<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\FileCheck;
use PHPUnit\Framework\TestCase;

class FileCheckTest extends TestCase
{
    public function testFileDoesNotExists()
    {
        $check = new FileCheck('Test', 'doesnotexist.txt');
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('doesnotexist.txt does not exist!', $result->getError());
    }

    public function testSuccess()
    {
        $check = new FileCheck('Test', __DIR__.'/../test.log');
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
    }

    public function testWritable()
    {
        $check = new FileCheck('Test', __DIR__.'/../test.log');
        $check->isWritable();
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
    }
}
