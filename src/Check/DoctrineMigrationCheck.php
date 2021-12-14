<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Check\AbstractCheck;
use BretRZaun\StatusPage\Result;
use Doctrine\Migrations\DependencyFactory;

class DoctrineMigrationCheck extends AbstractCheck
{
    private DependencyFactory $dependencyFactory;

    public function __construct(string $label, DependencyFactory $dependencyFactory)
    {
        parent::__construct($label);
        $this->dependencyFactory = $dependencyFactory;
    }

    public function checkStatus(): Result
    {
        $calc = $this->dependencyFactory->getMigrationStatusCalculator();
        $count = $calc->getNewMigrations()->count();
        $aliasResolver = $this->dependencyFactory->getVersionAliasResolver();
        $version = $aliasResolver->resolveVersionAlias('latest');

        $result = new Result($this->label);
        if ($version) {
            $result->setDetails('current version: '.$version);
        }
        if ($count > 0) {
            $result->setError($count.' outstanding migration(s)');
        }
        return $result;
    }
}