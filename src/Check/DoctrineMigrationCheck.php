<?php
namespace BretRZaun\StatusPage\Check;

use Throwable;
use BretRZaun\StatusPage\Check\AbstractCheck;
use BretRZaun\StatusPage\Result;
use Doctrine\Migrations\DependencyFactory;

class DoctrineMigrationCheck extends AbstractCheck
{
    public function __construct(string $label, private readonly DependencyFactory $dependencyFactory)
    {
        parent::__construct($label);
    }

    public function checkStatus(): Result
    {
        $result = new Result($this->label);

        try {
            $calc = $this->dependencyFactory->getMigrationStatusCalculator();
            $count = $calc->getNewMigrations()->count();
            $aliasResolver = $this->dependencyFactory->getVersionAliasResolver();
            $version = $aliasResolver->resolveVersionAlias('latest');
        
            if ((string)$version !== '') {
                $result->setDetails('current version: '.$version);
            }
            if ($count > 0) {
                $result->setError($count.' outstanding migration(s)');
            }
        } catch (Throwable $e) {
            $result->setError($e->getMessage());
        }
        
        return $result;
    }
}
