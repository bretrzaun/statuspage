<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;
use Exception;
use function PHPUnit\Framework\isEmpty;

class ElasticsearchCheck extends AbstractCheck
{

    /**
     * @var \Elasticsearch\Client|\Elastic\Elasticsearch\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $indices;

    /**
     * Constructor
     *
     * @param string $label
     * @param \Elasticsearch\Client|\Elastic\Elasticsearch\Client $client
     * @param array $indices Indices to check for
     */
    public function __construct(string $label, \Elasticsearch\Client|\Elastic\Elasticsearch\Client $client, array $indices = [])
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
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        try {
            $info = $this->client->info();
            $versionParts = explode('.', $info['version']['number']);
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
