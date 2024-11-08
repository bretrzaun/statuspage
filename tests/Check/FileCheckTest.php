<?php
namespace BretRZaun\StatusPage\Tests\Check;

use PHPUnit\Framework\Attributes\DataProvider;
use BretRZaun\StatusPage\Check\FileCheck;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

class FileCheckTest extends TestCase {

    protected static $testFile;

    public static function setUpBeforeClass(): void
    {
        self::$testFile = tempnam(sys_get_temp_dir(), __FUNCTION__);
        if (false === file_put_contents(
            self::$testFile,
            self::class . PHP_EOL .
            'This is a test file for unit tests' . PHP_EOL .
            'And it has to contain: ' . PHP_EOL .
            '   Success: test passed' . PHP_EOL
        )) {
            throw new Exception('Can not write tmp file');
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$testFile)) {
            unlink(self::$testFile);
        }
    }


    public function testFileExists(): void
    {
        $check = new FileCheck(__METHOD__, self::$testFile);
        $result = $check->checkStatus();
        $this->assertTrue($result->isSuccess());

        $check = new FileCheck(__METHOD__, '/file/does/not.exist');
        $result = $check->checkStatus();
        $this->assertFalse($result->isSuccess());
    }

    public function testAge(): void
    {
        // file is newer than one minute
        $check = new FileCheck(__METHOD__, self::$testFile);
        $check->setMaxage(1);
        $result = $check->checkStatus();
        $this->assertTrue($result->isSuccess());

        // manipulate timestamp - file should be older
        $res = touch(self::$testFile, time()-3600,  time()-3600 );
        $this->assertTrue( $res, 'touch() failed' );
        // do not forget to clear PHP cache
        clearstatcache(true, self::$testFile);
        $result = $check->checkStatus();
        $this->assertFalse($result->isSuccess());
    }

    /**
     * DataProvider
     * @return array
     */
    public static function dataPattern()
    {
        return [
            ['Error', true],
            ['Error.*Test', true],
            ['Success:.*passed', false],
        ];
    }

    /**
     * @param string $pattern
     * @param bool $expected
     */
    #[DataProvider('dataPattern')]
    public function testPattern($pattern, $expected): void
    {
        $check = new FileCheck(__METHOD__, self::$testFile);
        $check->setUnwantedRegex($pattern);
        $result = $check->checkStatus();
        $this->assertEquals($expected, $result->isSuccess());
    }


    public function testWriteable(): void
    {
        $check = new FileCheck(__METHOD__, self::$testFile);
        $check->setWritable();
        $result = $check->checkStatus();
        $this->assertTrue($result->isSuccess());

        if (posix_getuid() === 0){
            $this->markTestSkipped('This test can not run as root user');
        } else {
            $this->assertTrue(chmod(self::$testFile, 0444), 'Can not modify permissions');
            $result = $check->checkStatus();
            $this->assertFalse($result->isSuccess());
        }
    }

}
