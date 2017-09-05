<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\CallbackCheck;
use PHPUnit\Framework\TestCase;

class CallbackCheckTest extends TestCase
{

    public function testSuccess()
    {
        $check = new CallbackCheck('callback test', function() {
            return true;
        });
        $result = $check->check();

        $this->assertTrue($result->getSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure()
    {
        $check = new CallbackCheck('callback test', function() {
            return 'an error occured!';
        });
        $result = $check->check();

        $this->assertFalse($result->getSuccess());
        $this->assertEquals('an error occured!', $result->getError());
    }
}
