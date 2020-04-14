<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

abstract class AbstractCheck implements CheckInterface
{
    protected $label;

    public function __construct($label)
    {
        $this->label = $label;
    }

    abstract public function check(): Result;
}
