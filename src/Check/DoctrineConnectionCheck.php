<?php

namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;
use Doctrine\DBAL\Connection;

class DoctrineConnectionCheck extends AbstractCheck
{

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(string $label, Connection $db)
    {
        parent::__construct($label);
        $this->db = $db;
    }

    /**
     * Check Doctrine connection
     *
     * @return Result
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        try {
            $this->db->getNativeConnection();
        } catch (\Throwable $e) {
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
