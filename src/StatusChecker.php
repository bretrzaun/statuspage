<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\AbstractCheck;

class StatusChecker implements StatusCheckerInterface
{

    /**
     * results of the registered checks
     * @var array
     */
    protected $results = array();


    protected $checks = array();

    public function addCheck(AbstractCheck $checker)
    {
        $this->checks[] = $checker;
    }

    public function check()
    {
        foreach($this->checks as $checker) {
            $this->results[] = $checker->check();
        }
    }

    public function getResults()
    {
        return $this->results;
    }

    public function hasErrors()
    {
        $error = false;
        foreach($this->results as $result) {
            if (!$result->getSuccess()) {
                $error = true;
                break;
            }
        }
        return $error;
    }
}
