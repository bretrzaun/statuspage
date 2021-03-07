<?php

namespace BretRZaun\StatusPage\Tests;

use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Result;
use PHPUnit\Framework\TestCase;
use BretRZaun\StatusPage\StatusChecker;
use Psr\Log\Test\TestLogger;

class StatusLogTest extends TestCase
{
    public function testLogger(): void
    {
        $statusChecker = new StatusChecker();
        $logger = new TestLogger();
        $statusChecker->setLogger($logger);

        $statusChecker->addCheck(new CallbackCheck('Test 1', function($label) {
            return new Result($label);
        }));
        $statusChecker->addCheck(new CallbackCheck('Test 2', function($label) {
            $result = new Result($label);
            $result->setError('this check failed!');
            return $result;
        }));
        $statusChecker->check();

        $this->assertTrue($logger->hasAlertRecords());
        $this->assertEquals('Test 2: this check failed!', $logger->recordsByLevel['alert'][0]['message']);
    }
}