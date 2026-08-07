<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\CheckInterface;
use BretRZaun\StatusPage\Enum\ResultType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class StatusChecker implements StatusCheckerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * registered ungrouped checks
     */
    protected ?StatusCheckerGroup $ungroupedChecks = null;

    /**
     * @var StatusCheckerGroup[]
     */
    protected array $results = [];

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

    public function getWorstResultType(): ResultType
    {
        $worstResultType = ResultType::SUCCESS;
        foreach ($this->results as $group) {
            if ($group->getWorstResultType() == ResultType::ERROR) {
                $worstResultType = ResultType::ERROR;
                break;
            }
            if ($group->getWorstResultType() == ResultType::WARNING) {
                $worstResultType = ResultType::WARNING;
            }
        }
        return $worstResultType;
    }
}
