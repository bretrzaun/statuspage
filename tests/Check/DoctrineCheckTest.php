<?php

class DoctrineCheckTest extends PHPUnit_Framework_TestCase
{

    public function testSuccess()
    {
        $db = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $db->expects($this->once())
            ->method('connect');

        $check = new \BretRZaun\StatusPage\Check\DoctrineCheck('Test', $db);
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure()
    {
        $db = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $db->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new Exception('test failure')))
        ;

        $check = new \BretRZaun\StatusPage\Check\DoctrineCheck('Test', $db);
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('test failure', $result->getError());
    }
}
