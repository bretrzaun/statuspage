<?php
namespace BretRZaun\StatusPage\Check;

abstract class AbstractCheck
{
    protected $label;

    public function __construct($label)
    {
        $this->label = $label;
    }

    abstract public function check();
}
