<?php
namespace BretRZaun\StatusPage\Tests\Check;

use PHPUnit\Framework\TestCase;
use Doctrine\Migrations\Version\Version;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Version\AliasResolver;
use BretRZaun\StatusPage\Check\DoctrineMigrationCheck;
use Doctrine\Migrations\Metadata\AvailableMigrationsList;
use Doctrine\Migrations\Version\MigrationStatusCalculator;

class DoctrineMigrationCheckTest extends TestCase
{
    public function testMigration(): void
    {
        $version = new Version('123');
        $newMigrations = new AvailableMigrationsList([$version]);

        $migrationStatusCalculator = $this->createMock(MigrationStatusCalculator::class);
        $migrationStatusCalculator
            ->expects($this->once())
            ->method('getNewMigrations')
            ->willReturn($newMigrations)
            ;

        $currentVersion = new Version('current-version');
        $versionAliasResolver = $this->createMock(AliasResolver::class);
        $versionAliasResolver
            ->expects($this->once())
            ->method('resolveVersionAlias')
            ->with('latest')
            ->willReturn($currentVersion)
            ;

        $dependencyFactory = $this->createMock(DependencyFactory::class);
        $dependencyFactory
            ->expects($this->once())
            ->method('getMigrationStatusCalculator')
            ->willReturn($migrationStatusCalculator)
            ;
        $dependencyFactory
            ->expects($this->once())
            ->method('getVersionAliasResolver')
            ->willReturn($versionAliasResolver)
            ;

        $check = new DoctrineMigrationCheck('db schema', $dependencyFactory);
        $result = $check->checkStatus();
        $this->assertEquals('db schema', $result->getLabel());
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('1 outstanding migration(s)', $result->getError());
        $this->assertEquals('current version: current-version', $result->getDetails());
    }
}