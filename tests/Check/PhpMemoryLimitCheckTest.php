<?php

namespace BretRZaun\StatusPage\Tests\Check;

use PHPUnit\Framework\Attributes\DataProvider;
use BretRZaun\StatusPage\Check\PhpMemoryLimitCheck;
use PHPUnit\Framework\TestCase;

class PhpMemoryLimitCheckTest extends TestCase
{
    /** @var PhpMemoryLimitCheck */
    protected $checker;

    protected function setUp(): void
    {
        $this->checker = new PhpMemoryLimitCheck('Test', 1024);
    }

    public static function getTestSizeStringConversion(): array
    {
        return [
            ['1024M', 1024],
            ['128m', 128],
            ['2048K', 2],
            ['512k', 0.5],
            ['1G', 1024],
            ['2G', 2048],
        ];
    }

    /**
     * @param string $sizeString
     * @param int|float $expectedMegabytes
     */
    #[DataProvider('getTestSizeStringConversion')]
    public function testSizeStringConversion($sizeString, $expectedMegabytes): void
    {
        $actual = $this->checker->getMegabytesFromSizeString($sizeString);
        $this->assertEquals($expectedMegabytes, $actual);
    }
}
