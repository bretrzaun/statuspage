<?php

namespace BretRZaun\StatusPage\Tests\Check;

use PHPUnit\Framework\Attributes\DataProvider;
use BretRZaun\StatusPage\Check\PhpIniCheck;
use PHPUnit\Framework\TestCase;

class PhpIniCheckTest extends TestCase
{

    public static function getTestData(): array
    {
        return [
            // php.ini default value: true
            ['allow_url_fopen', PhpIniCheck::TypeBoolean, true, null, true],
            ['allow_url_fopen', PhpIniCheck::TypeBoolean, false, null, false],

            // php.ini default value: off
            ['allow_url_include', PhpIniCheck::TypeBoolean, true, null, false],
            ['allow_url_include', PhpIniCheck::TypeBoolean, false, null, true],

            // php.ini default value: 128M
            ['memory_limit', PhpIniCheck::TypeMemory, 64, null, true],
            ['memory_limit', PhpIniCheck::TypeMemory, 128, null, true],
            ['memory_limit', PhpIniCheck::TypeMemory, 5000, null, false],

            // php.ini default value: 1000
            ['max_input_vars', PhpIniCheck::TypeNumber, 1000, null, true],
            ['max_input_vars', PhpIniCheck::TypeNumber, 1000, 1000, true],
            ['max_input_vars', PhpIniCheck::TypeNumber, 1000, 1111, true],
            ['max_input_vars', PhpIniCheck::TypeNumber, 1111, null, false],
            ['max_input_vars', PhpIniCheck::TypeNumber, 0, 999, false],
            ['max_input_vars', PhpIniCheck::TypeNumber, 0, 1000, true],

            // php.ini default value: UTF-8
            ['default_charset', PhpIniCheck::TypeRegex, 'UTF-[1-9]+', null, true],
            ['default_charset', PhpIniCheck::TypeRegex, 'UTC[1-9]+', null, false],

            // php.ini default value: UTF-8
            ['default_charset', PhpIniCheck::TypeString, 'UTF-8', null, true],
            ['default_charset', PhpIniCheck::TypeString, 'UTF-16', null, false],
        ];
    }

    /**
     * @param string $varName
     * @param string $varType
     * @param bool $expected
     */
    #[DataProvider('getTestData')]
    public function testPhpIniCheck($varName, $varType, mixed $minValue, mixed $maxValue, $expected): void
    {
        $checker = new PhpIniCheck('UnitTest_'.$varName, $varName, $varType, $minValue, $maxValue);
        $result = $checker->checkStatus();
        $this->assertEquals($expected, $result->isSuccess(), (string)$result->getError());
    }

}
