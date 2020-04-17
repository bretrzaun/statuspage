<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;
use Elasticsearch\Client;
use Exception;

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
    public function __construct(string $label, Client $client, array $indices = [])
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
    public function check(): Result
    {
        $result = new Result($this->label);
        try {
            if ($this->client->ping() !== true) {
                $result->setSuccess(false);
                return $result;
            }
            foreach ($this->indices as $index) {
                if (!$this->client->indices()->exists(['index' => $index])) {
                    $result->setSuccess(false);
                    $result->setError("Index '$index' does not exist");
                }
            }
        } catch (Exception $e) {
            $result->setSuccess(false);
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
