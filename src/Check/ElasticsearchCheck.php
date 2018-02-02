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
     * @var array
     */
    protected $indices;

    /**
     * Constructor
     *
     * @param $label
     * @param Client $client
     * @param array $indices Indices to check for
     */
    public function __construct($label, Client $client, array $indices = [])
    {
        parent::__construct($label);
        $this->client = $client;
        $this->indices = $indices;
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

        foreach ($this->indices as $index) {
            if (!$this->client->indices()->exists(['index' => $index])) {
                $result->setError("Index '$index' does not exist");
            }
        }
        return $result;
    }
}
