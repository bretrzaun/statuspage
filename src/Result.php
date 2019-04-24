<?php
namespace BretRZaun\StatusPage;

class Result
{
    protected $success = true;
    protected $error;
    protected $label;
    protected $details;

    public function __construct($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setSuccess($success): void
    {
        $this->success = $success;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setError($error): void
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setDetails($details): void
    {
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
