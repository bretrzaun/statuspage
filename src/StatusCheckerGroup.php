<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\AbstractCheck;

class StatusCheckerGroup
{
    /** @var string */
    protected $title;

    /** @var AbstractCheck[] */
    protected $checks = [];

    /** @var Result[] */
    protected $results = array();


    /**
     * StatusCheckerGroup constructor.
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Adds check to the group.
     *
     * @param AbstractCheck $checker
     */
    public function addCheck(AbstractCheck $checker)
    {
        $this->checks[] = $checker;
    }

    /**
     * Runs all added checks.
     */
    public function check()
    {
        foreach($this->checks as $checker) {
            $this->results[] = $checker->check();
        }
    }

    /**
     * Returns results of all run checks.
     *
     * @return Result[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Returns if there is an erroneous result in this group.
     *
     * @return bool
     */
    public function hasErrors(): bool
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
