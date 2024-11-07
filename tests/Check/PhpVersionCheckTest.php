<?php

namespace BretRZaun\StatusPage\Tests\Check;

use PHPUnit\Framework\Attributes\DataProvider;
use BretRZaun\StatusPage\Check\PhpVersionCheck;
use PHPUnit\Framework\TestCase;

class PhpVersionCheckTest extends TestCase
{
    public static function getTestPhpVersionCheck(): array
    {
        return [
            [null, null, '7.1.0', true],
            ['7.0.0', '7.2.0', '7.1.0', true],
            ['7.0.0', '7.2.0', '7.0.0', true],
            ['7.0.0', '7.2.0', '7.2.0', false],
            ['7.0.0', '7.2.0', '5.6.0', false],
            ['7.0.0', '7.2.0', '7.3.0', false],
            [null, '7.2.0', '5.6.0', true],
            [null, '7.2.0', '7.1.0', true],
            [null, '7.2.0', '7.3.0', false],
            ['7.0.0', null, '5.6.0', false],
            ['7.0.0', null, '7.1.0', true],
            ['7.0.0', null, '7.3.0', true],
        ];
    }

    /**
     * @param string|null $greaterEquals
     * @param string|null $lessThan
     * @param string $phpVersion
     * @param bool $expected
     */
    #[DataProvider('getTestPhpVersionCheck')]
    public function testPhpVersionCheck($greaterEquals, $lessThan, $phpVersion, $expected): void
    {
        $mock = $this->getMockBuilder(PhpVersionCheck::class)
            ->setConstructorArgs(['Test', $greaterEquals, $lessThan])
            ->onlyMethods(['getPhpVersion'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getPhpVersion')
            ->willReturn($phpVersion);

        /** @var PhpVersionCheck $mock */
        $result = $mock->checkStatus();
        $actual = $result->isSuccess();

        $this->assertEquals($expected, $actual);
    }
}
