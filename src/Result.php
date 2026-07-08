<?php

namespace BretRZaun\StatusPage;

use BretRZaun\StatusPage\Enum\ResultType;

class Result
{
    protected ResultType $type = ResultType::SUCCESS;
    protected ?string $error = null;
    protected ?string $details = null;
    protected ?float $duration = null; 

    public function __construct(protected string $label)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setType(ResultType $type): void
    {
        $this->type = $type;
    }

    public function getType(): ResultType
    {
        return $this->type;
    }

    public function setSuccess(bool $success): void
    {
        if ($success) {
            $this->type = ResultType::SUCCESS;
        }
    }

    public function isSuccess(): bool
    {
        return $this->type === ResultType::SUCCESS;
    }

    public function setError(string $error): void
    {
        $this->type = ResultType::ERROR;
        $this->error = $error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setWarning(string $text): void
    {
        $this->setType(ResultType::WARNING);
        $this->setDetails($text);
    }

    public function setDetails(string $details): void
    {
        $this->details = $details;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }
        
    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Returns the execution duration in milliseconds, or null if not measured.
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }
}
