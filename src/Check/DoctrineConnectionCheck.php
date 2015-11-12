<?php

namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class DoctrineConnectionCheck extends AbstractCheck
{

    protected $db;

    public function __construct($label, \Doctrine\DBAL\Connection $db)
    {
        $this->label = $label;
        $this->db = $db;
    }

    public function check()
    {
        $result = new Result($this->label);
        try {
            $this->db->connect();
        } catch (\Exception $e) {
            $result->setSuccess(false);
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
