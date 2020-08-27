<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\DoctrineConnectionCheck;
use Doctrine\DBAL\Connection;
use Exception;
use PHPUnit\Framework\TestCase;

class DoctrineConnectionCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $db->expects($this->once())
            ->method('connect');

        $check = new DoctrineConnectionCheck('Test', $db);
        $result = $check->checkStatus();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $db->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new Exception('test failure')))
        ;

        $check = new DoctrineConnectionCheck('Test', $db);
        $result = $check->checkStatus();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('test failure', $result->getError());
    }
}
