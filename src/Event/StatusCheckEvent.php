<?php
namespace BretRZaun\StatusPage\Event;

use Symfony\Contracts\EventDispatcher\Event;
use BretRZaun\StatusPage\StatusChecker;

class StatusCheckEvent extends Event
{
    /**
     * @var bool
     */
    private $showDetails;

    public function __construct(private StatusChecker $checker)
    {
    }

    public function getChecker(): StatusChecker
    {
        return $this->checker;
    }

    public function setShowDetails(bool $showDetails): void
    {
        $this->showDetails = $showDetails;
    }

    public function getShowDetails(): bool
    {
        return $this->showDetails;
    }
}