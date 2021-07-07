<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Result;
use PHPUnit\Framework\TestCase;

class CallbackCheckTest extends TestCase
{

    public function testSuccess(): void
    {
        $check = new CallbackCheck('callback test', function() {
            return true;
        });
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $check = new CallbackCheck('callback test', function() {
            return 'an error occured!';
        });
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('an error occured!', $result->getError());
    }

    public function testReturnResult(): void
    {
        $check = new CallbackCheck('callback test', function($label) {
            $result = new Result($label);
            $result->setSuccess(true);
            $result->setDetails('ok - with comment');
            return $result;
        });
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('ok - with comment', $result->getDetails());

    }
}
