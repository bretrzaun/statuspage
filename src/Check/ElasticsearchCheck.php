<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;
use Elasticsearch\Client;

class ElasticsearchCheck extends AbstractCheck
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor
     *
     * @param $label
     * @param Client $client
     */
    public function __construct($label, Client $client)
    {
        parent::__construct($label);
        $this->client = $client;
    }

    /**
     * Check callback
     *
     * @return Result
     */
    public function check()
    {
        $result = new Result($this->label);
        if ($this->client->ping() !== true) {
            $result->setSuccess(false);
        }
        return $result;
    }
}
