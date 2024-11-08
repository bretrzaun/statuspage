<?php

namespace BretRZaun\StatusPage\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use BretRZaun\StatusPage\Check\AbstractCheck;
use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Result;
use BretRZaun\StatusPage\StatusChecker;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\DomCrawler\Crawler;

class StatusPageTest extends TestCase
{

    protected function render(StatusChecker $checker, string $title): string
    {
        // use the built-in Twig template
        $loader = new FilesystemLoader('resources/views/');
        $twig = new Environment($loader, ['autoescape' => false]);

        $checker->check();
        return $twig->render(
            'status.twig',
            [
                'results' => $checker->getResults(),
                'title' => $title
            ]
        );
    }

    public function testNoChecks(): void
    {
        $html = $this->render(new StatusChecker(), 'TestPage');
        $crawler = new Crawler($html);

        $this->assertCount(1, $crawler->filter('a:contains("TestPage")'));
    }

    public function testSuccess(): void
    {
        $mock = $this->getMockBuilder(AbstractCheck::class)
            ->setConstructorArgs(['TestCheck'])
            ->getMock();

        $result = new Result('TestCheck');
        $result->setSuccess(true);

        $mock->expects($this->once())
            ->method('checkStatus')
            ->willReturn($result);

        $statusChecker = new StatusChecker();
        $statusChecker->addCheck($mock);
        $html = $this->render($statusChecker, 'TestPage');

        $crawler = new Crawler($html);
        $this->assertCount(1, $crawler->filter('th:contains("TestCheck")'));
        $this->assertCount(1, $crawler->filter('td:contains("OK")'));
    }

    public function testFailer(): void
    {
        $mock = $this->getMockBuilder(AbstractCheck::class)
            ->setConstructorArgs(['TestCheck'])
            ->getMock();

        $result = new Result('TestCheck');
        $result->setSuccess(false);
        $result->setError('Failed');

        $mock->expects($this->once())
            ->method('checkStatus')
            ->willReturn($result);

        $statusChecker = new StatusChecker();
        $statusChecker->addCheck($mock);
        $html = $this->render($statusChecker, 'TestPage');

        $crawler = new Crawler($html);
        $this->assertCount(1, $crawler->filter('th:contains("TestCheck")'));
        $this->assertCount(1, $crawler->filter('td:contains("Failed")'));
    }

    /**
     * DataProvider for testShowDetails.
     */
    public static function getTestShowDetails(): array
    {
        return [
            [false, false, 'System is up and running', 'my test detail'],
            [true, false, 'System is having some issues', 'my test detail'],
            [false, true, 'my test detail', 'System is up and running'],
            [true, true, 'my test detail', 'System is having some issues'],
        ];
    }

    /**
     * Checks if showing / hiding details works
     */
    #[DataProvider('getTestShowDetails')]
    public function testShowDetails($hasFailure, $showDetailsParam, $htmlContains, $htmlNotContains): void
    {
        $checker = new StatusChecker();
        $check = new CallbackCheck('my test detail', function (Result $result) use ($hasFailure): void {
            if ($hasFailure) {
                $result->setError('an error occured!');
            }
        });
        $checker->addCheck($check);
        $checker->check();

        $loader = new FilesystemLoader(__DIR__ . '/../resources/views/');
        $twig = new Environment($loader, ['autoescape' => false]);
        $content = $twig->render(
            'status.twig',
            [
                'results' => $checker->getResults(),
                'title' => 'My test status page',
                'showDetails' => $showDetailsParam,
            ]
        );

        $this->assertStringContainsString($htmlContains, $content);
        $this->assertStringNotContainsString($htmlNotContains, $content);
    }
}
