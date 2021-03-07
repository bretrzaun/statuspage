<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\CheckInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class StatusChecker implements StatusCheckerInterface, LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * registered ungrouped checks
     * @var StatusCheckerGroup
     */
    protected $ungroupedChecks;

    /**
     * @var StatusCheckerGroup[]
     */
    protected $results = [];

    public function addCheck(CheckInterface $checker): void
    {
        if ($this->ungroupedChecks === null) {
            $this->ungroupedChecks = new StatusCheckerGroup('');
            $this->addGroup($this->ungroupedChecks);
        }
        $this->ungroupedChecks->addCheck($checker);
    }

    public function addGroup(StatusCheckerGroup $group): void
    {
        if ($this->logger) {
            $group->setLogger($this->logger);
        }
        $this->results[] = $group;
    }

    public function check(): void
    {
        foreach($this->results as $group) {
            $group->check();
        }
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function hasErrors(): bool
    {
        $error = false;
        foreach($this->results as $group) {
            if ($group->hasErrors()) {
                $error = true;
                break;
            }
        }
        return $error;
    }
}
