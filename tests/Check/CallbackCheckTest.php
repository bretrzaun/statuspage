<?php
namespace BretRZaun\StatusPage\Tests\Check;

use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Result;
use PHPUnit\Framework\TestCase;

class CallbackCheckTest extends TestCase
{
    public function testSuccess(): void
    {
        $check = new CallbackCheck('callback test', fn() => true);
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getError());
    }

    public function testFailure(): void
    {
        $check = new CallbackCheck('callback test', function(Result $result): void {
            $result->setError('an error occured!');
        });
        $result = $check->checkStatus();

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('an error occured!', $result->getError());
    }

    public function testReturnResult(): void
    {
        $check = new CallbackCheck('callback test', function(Result $result): void {
            $result->setSuccess(true);
            $result->setDetails('ok - with comment');
        });
        $result = $check->checkStatus();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('ok - with comment', $result->getDetails());
    }
}
