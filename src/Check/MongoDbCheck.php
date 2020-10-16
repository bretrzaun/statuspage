<?php
namespace BretRZaun\StatusPage\Check;
use MongoDB\Client;

use BretRZaun\StatusPage\Result;

class MongoDbCheck extends AbstractCheck
{
    private $client;
    private $databases = [];
    private $collections = [];

    public function __construct(string $label, Client $client)
    {
        parent::__construct($label);
        $this->client = $client;
    }

    public function ensureDatabaseExists(string $database)
    {
        $this->databases[] = $database;
    }

    public function ensureDatabaseHasCollecion(string $database, string $collection)
    {
        $this->ensureDatabaseExists($database);
        if (!isset($this->collections[$database])) {
            $this->collections[$database] = [];
        }
        $this->collections[$database][] = $collection;
    }

    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        try {
            $dbs = $this->client->listDatabaseNames()->getArrayCopy();

            if (count($this->databases) > 0) {
                $this->checkDatabases($dbs);
            }
            if (count($this->collections) > 0) {
                $this->checkCollections($dbs);
            }
        } catch(\Exception $e) {
            $result->setError($e->getMessage());
        }
        return $result;
    }

    private function checkDatabases($databases)
    {
        foreach($this->databases as $database) {
            if (!in_array($database, $databases)) {
                throw new \RuntimeException('Database '.$database.' does not exist');
            }
        }
    }

    private function checkCollections(array $databases)
    {
        foreach($this->collections as $databaseName => $collections) {
            $database = $this->client->selectDatabase($databaseName);
            $collectionNames = $database->listCollectionNames()->getArrayCopy();
            foreach($collections as $collection) {
                if (!in_array($collection, $collectionNames)) {
                    throw new \RuntimeException('Collection '.$collection.' does not exist in database '.$databaseName);
                }
            }
        }
    }

}