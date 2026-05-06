<?php
namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Check\CheckInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Stopwatch\Stopwatch;

class StatusCheckerGroup implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var CheckInterface[] */
    protected array$checks = [];

    /** @var Result[] */
    protected array $results = [];

    private Stopwatch $stopwatch;

    /**
     * StatusCheckerGroup constructor.
     */
    public function __construct(protected string $title)
    {
        $this->stopwatch = new Stopwatch();
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
            $eventName = $checker::class;

            $this->stopwatch->start($eventName);
            $result = $checker->checkStatus();
            $result->setDuration($this->stopwatch->stop($eventName)->getDuration());

            if ($this->logger) {
                $context = [
                    'details'  => $result->getDetails(),
                    'duration' => $result->getDuration(),
                ];
                if ($result->isSuccess()) {
                    $this->logger->info($result->getLabel().': OK', $context);
                } else {
                    $this->logger->alert($result->getLabel().': '.$result->getError(), $context);
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
        foreach ($this->results as $result) {
            if (!$result->isSuccess()) {
                return true;
            }
        }
        return false;
    }
}
