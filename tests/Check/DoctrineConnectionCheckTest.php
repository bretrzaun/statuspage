<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\DoctrineConnectionCheck;
use Doctrine\DBAL\Connection;

class DoctrineConnectionCheckTest extends \PHPUnit_Framework_TestCase
{

    public function testSuccess()
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $db->expects($this->once())
            ->method('connect');

        $check = new DoctrineConnectionCheck('Test', $db);
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure()
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $db->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new \Exception('test failure')))
        ;

        $check = new DoctrineConnectionCheck('Test', $db);
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('test failure', $result->getError());
    }
}
