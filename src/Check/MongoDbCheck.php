<?php
namespace BretRZaun\StatusPage\Check;
use Exception;
use RuntimeException;
use MongoDB\Client;

use BretRZaun\StatusPage\Result;

class MongoDbCheck extends AbstractCheck
{
    private $databases = [];
    private $collections = [];

    public function __construct(string $label, private readonly Client $client)
    {
        parent::__construct($label);
    }

    public function ensureDatabaseExists(string $database)
    {
        $this->databases[] = $database;
    }

    public function ensureDatabaseHasCollection(string $database, string $collection)
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
            $databases = $this->listDatabaseNames();
            if (count($this->databases) > 0) {
                $this->checkDatabases($databases);
            }
            if (count($this->collections) > 0) {
                $this->checkCollections();
            }
        } catch(Exception $e) {
            $result->setError($e->getMessage());
        }
        return $result;
    }

    private function listDatabaseNames(): array
    {
        $databaseNames = [];
        $iterator = $this->client->listDatabases();
        foreach ($iterator as $dbInfo) {
            $databaseNames[] = $dbInfo->getName();
        }
        return $databaseNames;
    }

    private function listCollectionNames(string $databaseName): array
    {
        $database = $this->client->selectDatabase($databaseName);
        $collectionNames = [];
        $iterator = $database->listCollections();
        foreach ($iterator as $collectionInfo) {
            $collectionNames[] = $collectionInfo->getName();
        }
        return $collectionNames;
    }

    private function checkDatabases($databases)
    {
        foreach($this->databases as $database) {
            if (!in_array($database, $databases)) {
                throw new RuntimeException('Database '.$database.' does not exist');
            }
        }
    }

    private function checkCollections()
    {
        foreach ($this->collections as $databaseName => $collections) {
            $collectionNames = $this->listCollectionNames($databaseName);
            foreach ($collections as $collection) {
                if (!in_array($collection, $collectionNames)) {
                    throw new RuntimeException('Collection '.$collection.' does not exist in database '.$databaseName);
                }
            }
        }
    }
}