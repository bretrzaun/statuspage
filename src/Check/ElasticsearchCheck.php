<?php
namespace BretRZaun\StatusPage\Check;

use Elastic\Elasticsearch\Client;
use BretRZaun\StatusPage\Result;
use Exception;

class ElasticsearchCheck extends AbstractCheck
{
    /**
     * Constructor
     *
     * @param array $indices Indices to check for
     */
    public function __construct(string $label, protected Client $client, protected array $indices = [])
    {
        parent::__construct($label);
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
