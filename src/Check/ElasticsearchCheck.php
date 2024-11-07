<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;
use Exception;

class ElasticsearchCheck extends AbstractCheck
{
    protected \Elastic\Elasticsearch\Client $client;

    protected array $indices;

    /**
     * Constructor
     *
     * @param string $label
     * @param \Elastic\Elasticsearch\Client $client
     * @param array $indices Indices to check for
     */
    public function __construct(string $label, \Elastic\Elasticsearch\Client $client, array $indices = [])
    {
        parent::__construct($label);

        $this->client = $client;
        $this->indices = $indices;
    }

    /**
     * Check callback
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        try {
            $info = $this->client->info();
            $versionParts = explode('.', (string) $info['version']['number']);
            $esMajorVersion = (int)array_shift($versionParts);
            if ($esMajorVersion >= 8) {
                if ($this->client->ping()->asBool() !== true) {
                    $result->setError("Elasticsearch is not reachable (ping failed)");
                    return $result;
                }
            } else {
                if ($this->client->ping() !== true) {
                    $result->setError("Elasticsearch is not reachable (ping failed)");
                    return $result;
                }
            }

            foreach ($this->indices as $index) {
                if ($esMajorVersion >= 8) {
                    if (!$this->client->indices()->exists(['index' => $index])->asBool()) {
                        $result->setError("Index '$index' does not exist");
                    }
                } else {
                    if (!$this->client->indices()->exists(['index' => $index])) {
                        $result->setError("Index '$index' does not exist");
                    }
                }
            }
        } catch (Exception $e) {
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
