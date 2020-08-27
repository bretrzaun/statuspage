<?php
namespace BretRZaun\StatusPage;

class Result
{
    protected $success = true;
    protected $error;
    protected $label;
    protected $details;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setError(string $error): void
    {
        $this->setSuccess(false);
        $this->error = $error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setDetails(string $details): void
    {
        $this->details = $details;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }
}
