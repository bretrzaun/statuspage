<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\AbstractCheck;

class StatusChecker implements StatusCheckerInterface
{
    /**
     * registered ungrouped checks
     * @var StatusCheckerGroup[]
     */
    protected $ungroupedChecks;

    /**
     * @var StatusCheckerGroup[]
     */
    protected $results = array();

    public function addCheck(AbstractCheck $checker): void
    {
        if (!$this->ungroupedChecks) {
            $this->ungroupedChecks = new StatusCheckerGroup('');
            $this->results[] = $this->ungroupedChecks;
        }
        $this->ungroupedChecks->addCheck($checker);
    }

    public function addGroup(StatusCheckerGroup $group): void
    {
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
