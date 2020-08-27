<?php
namespace BretRZaun\StatusPage\Tests\Check;

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
            __CLASS__ . PHP_EOL .
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


    public function testFileExists()
    {
        $check = new FileCheck(__METHOD__, self::$testFile);
        $result = $check->checkStatus();
        $this->assertTrue($result->getSuccess());

        $check = new FileCheck(__METHOD__, '/file/does/not.exist');
        $result = $check->checkStatus();
        $this->assertFalse($result->getSuccess());
    }

    public function testAge()
    {
        // file is newer than one minute
        $check = new FileCheck(__METHOD__, self::$testFile);
        $check->setMaxage(1);
        $result = $check->checkStatus();
        $this->assertTrue($result->getSuccess());

        // manipulate timestamp - file should be older
        $res = touch(self::$testFile, time()-3600,  time()-3600 );
        $this->assertTrue( $res, 'touch() failed' );
        // do not forget to clear PHP cache
        clearstatcache(true, self::$testFile);
        $result = $check->checkStatus();
        $this->assertFalse($result->getSuccess());
    }

    /**
     * DataProvider
     * @return array
     */
    public function dataPattern()
    {
        return [
            ['Error', true],
            ['Error.*Test', true],
            ['Success:.*passed', false],
        ];
    }

    /**
     * @param $pattern
     * @param $expected
     * @dataProvider dataPattern
     */
    public function testPattern($pattern, $expected)
    {
        $check = new FileCheck(__METHOD__, self::$testFile);
        $check->setUnwantedRegex($pattern);
        $result = $check->checkStatus();
        $this->assertEquals($expected, $result->getSuccess());
    }


    public function testWriteable()
    {
        $check = new FileCheck(__METHOD__, self::$testFile);
        $check->setWritable();
        $result = $check->checkStatus();
        $this->assertTrue($result->getSuccess());

        if (posix_getuid() === 0){
            $this->markTestSkipped('This test can not run as root user');
        } else {
            $this->assertTrue(chmod(self::$testFile, 0444), 'Can not modify permissions');
            $result = $check->checkStatus();
            $this->assertFalse($result->getSuccess());
        }
    }

}
