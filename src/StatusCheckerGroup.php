<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\CheckInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class StatusCheckerGroup implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var string */
    protected $title;

    /** @var CheckInterface[] */
    protected $checks = [];

    /** @var Result[] */
    protected $results = [];


    /**
     * StatusCheckerGroup constructor.
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Adds check to the group.
     */
    public function addCheck(CheckInterface $checker): void
    {
        $this->checks[] = $checker;
    }

    /**
     * Runs all added checks.
     */
    public function check(): void
    {
        foreach ($this->checks as $checker) {
            $result = $checker->checkStatus();
            if ($this->logger) {
                if ($result->isSuccess()) {
                    $this->logger->info($result->getLabel().': OK', ['details' => $result->getDetails()]);
                } else {
                    $this->logger->alert($result->getLabel().': '.$result->getError(), ['details' => $result->getDetails()]);
                }
            }
            $this->results[] = $result;
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
     */
    public function hasErrors(): bool
    {
        $error = false;
        foreach ($this->results as $result) {
            if (!$result->isSuccess()) {
                $error = true;
                break;
            }
        }
        return $error;
    }
}
