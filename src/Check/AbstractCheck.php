<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

abstract class AbstractCheck implements CheckInterface
{
    protected $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    abstract public function checkStatus(): Result;
}
