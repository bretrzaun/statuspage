<?php

namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class DoctrineConnectionCheck extends AbstractCheck
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct($label, \Doctrine\DBAL\Connection $db)
    {
        parent::__construct($label);
        $this->db = $db;
    }

    /**
     * Check Doctrine connection
     *
     * @return Result
     */
    public function check(): Result
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
