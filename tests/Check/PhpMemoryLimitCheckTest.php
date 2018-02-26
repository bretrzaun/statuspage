<?php

namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\LogfileContentCheck;
use BretRZaun\StatusPage\Check\PhpMemoryLimitCheck;
use PHPUnit\Framework\TestCase;

class PhpMemoryLimitCheckTest extends TestCase
{
    /** @var PhpMemoryLimitCheck */
    protected $checker;

    public function setUp()
    {
        $this->checker = new PhpMemoryLimitCheck('Test', 1024);
    }

    public function getTestSizeStringConversion()
    {
        return array(
            array('1024M', 1024),
            array('128m', 128),
            array('2048K', 2),
            array('512k', 0.5),
            array('1G', 1024),
            array('2G', 2048),
        );
    }

    /**
     * @param $sizeString
     * @param $expectedMegabytes
     *
     * @dataProvider getTestSizeStringConversion
     */
    public function testSizeStringConversion($sizeString, $expectedMegabytes)
    {
        $actual = $this->checker->getMegabytesFromSizeString($sizeString);
        $this->assertEquals($expectedMegabytes, $actual);
    }
}
