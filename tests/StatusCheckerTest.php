<?php

namespace BretRZaun\StatusPage\Tests;

use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Enum\ResultType;
use BretRZaun\StatusPage\Result;
use BretRZaun\StatusPage\StatusChecker;
use BretRZaun\StatusPage\StatusCheckerGroup;
use PHPUnit\Framework\TestCase;

class StatusCheckerTest extends TestCase
{
    public function testEmptyChecker(): void
    {
        $checker = new StatusChecker();

        $this->assertFalse($checker->hasErrors());
        $this->assertSame(ResultType::SUCCESS, $checker->getWorstResultType());
    }

    public function testWarningOnly(): void
    {
        $checker = new StatusChecker();
        $checker->addCheck(new CallbackCheck('check', function (Result $result): void {
            $result->setWarning('careful');
        }));
        $checker->check();

        $this->assertFalse($checker->hasErrors());
        $this->assertSame(ResultType::WARNING, $checker->getWorstResultType());
    }

    public function testErrorOnly(): void
    {
        $checker = new StatusChecker();
        $checker->addCheck(new CallbackCheck('check', function (Result $result): void {
            $result->setError('failed');
        }));
        $checker->check();

        $this->assertTrue($checker->hasErrors());
        $this->assertSame(ResultType::ERROR, $checker->getWorstResultType());
    }

    /**
     * An error in one group must win over a warning in another group.
     */
    public function testErrorInOneGroupTakesPrecedenceOverWarningInAnother(): void
    {
        $checker = new StatusChecker();

        $warningGroup = new StatusCheckerGroup('warning group');
        $warningGroup->addCheck(new CallbackCheck('check', function (Result $result): void {
            $result->setWarning('careful');
        }));
        $checker->addGroup($warningGroup);

        $errorGroup = new StatusCheckerGroup('error group');
        $errorGroup->addCheck(new CallbackCheck('check', function (Result $result): void {
            $result->setError('failed');
        }));
        $checker->addGroup($errorGroup);

        $checker->check();

        $this->assertTrue($checker->hasErrors());
        $this->assertSame(ResultType::ERROR, $checker->getWorstResultType());
    }
}
