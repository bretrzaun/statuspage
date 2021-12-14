<?php

namespace BretRZaun\StatusPage\Tests;

use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Result;
use PHPUnit\Framework\TestCase;
use BretRZaun\StatusPage\StatusChecker;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class StatusLogTest extends TestCase
{
    public function testLogger(): void
    {
        $statusChecker = new StatusChecker();
        #$logger = new TestLogger();
        $logger = new Logger('test');
        $handler = new TestHandler();
        $logger->pushHandler($handler);
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

        $this->assertTrue($handler->hasAlertRecords());
        $records = $handler->getRecords();
        $this->assertEquals('Test 1: OK', $records[0]['message']);
        $this->assertEquals('INFO', $records[0]['level_name']);
        $this->assertEquals('Test 2: this check failed!', $records[1]['message']);
        $this->assertEquals('ALERT', $records[1]['level_name']);
    }
}