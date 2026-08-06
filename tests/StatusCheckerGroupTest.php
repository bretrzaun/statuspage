<?php

namespace BretRZaun\StatusPage\Tests;

use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Enum\ResultType;
use BretRZaun\StatusPage\Result;
use BretRZaun\StatusPage\StatusCheckerGroup;
use PHPUnit\Framework\TestCase;

class StatusCheckerGroupTest extends TestCase
{
    public function testEmptyGroup(): void
    {
        $group = new StatusCheckerGroup('test');

        $this->assertFalse($group->hasErrors());
        $this->assertFalse($group->hasWarnings());
        $this->assertSame(ResultType::SUCCESS, $group->getWorstResultType());
    }

    public function testSuccessOnly(): void
    {
        $group = new StatusCheckerGroup('test');
        $group->addCheck(new CallbackCheck('check', function (): void {
        }));
        $group->check();

        $this->assertFalse($group->hasErrors());
        $this->assertFalse($group->hasWarnings());
        $this->assertSame(ResultType::SUCCESS, $group->getWorstResultType());
    }

    public function testWarningOnly(): void
    {
        $group = new StatusCheckerGroup('test');
        $group->addCheck(new CallbackCheck('check', function (Result $result): void {
            $result->setWarning('careful');
        }));
        $group->check();

        $this->assertFalse($group->hasErrors());
        $this->assertTrue($group->hasWarnings());
        $this->assertSame(ResultType::WARNING, $group->getWorstResultType());
    }

    public function testErrorOnly(): void
    {
        $group = new StatusCheckerGroup('test');
        $group->addCheck(new CallbackCheck('check', function (Result $result): void {
            $result->setError('failed');
        }));
        $group->check();

        $this->assertTrue($group->hasErrors());
        $this->assertFalse($group->hasWarnings());
        $this->assertSame(ResultType::ERROR, $group->getWorstResultType());
    }

    /**
     * An error must win over a warning when both occur in the same group.
     */
    public function testErrorTakesPrecedenceOverWarning(): void
    {
        $group = new StatusCheckerGroup('test');
        $group->addCheck(new CallbackCheck('warning check', function (Result $result): void {
            $result->setWarning('careful');
        }));
        $group->addCheck(new CallbackCheck('error check', function (Result $result): void {
            $result->setError('failed');
        }));
        $group->check();

        $this->assertTrue($group->hasErrors());
        $this->assertTrue($group->hasWarnings());
        $this->assertSame(ResultType::ERROR, $group->getWorstResultType());
    }
}
