<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

interface CheckInterface
{
    public function checkStatus(): Result;
}
