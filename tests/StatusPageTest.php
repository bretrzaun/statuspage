<?php
namespace BretRZaun\StatusPage\Tests;

use BretRZaun\StatusPage\Check\AbstractCheck;
use BretRZaun\StatusPage\Check\CallbackCheck;
use BretRZaun\StatusPage\Result;
use BretRZaun\StatusPage\StatusChecker;
use BretRZaun\StatusPage\StatusPageServiceProvider;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\WebTestCase;
use Twig_Environment;
use Twig_Loader_Filesystem;

class StatusPageTest extends WebTestCase
{

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        unset($app['exception_handler']);
        $app->register(new TwigServiceProvider());
        return $app;
    }

    public function testNoChecks()
    {
        $this->app->register(new StatusPageServiceProvider(), [
            'statuspage.title' => 'TestPage'
        ]);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/status');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('a:contains("TestPage")'));
    }

    public function testSuccess()
    {
        $mock = $this->getMockBuilder(AbstractCheck::class)
            ->setConstructorArgs(array('TestCheck'))
            ->getMock();

        $result = new Result('TestCheck');
        $result->setSuccess(true);

        $mock->expects($this->once())
            ->method('check')
            ->willReturn($result);

        $this->app->register(new StatusPageServiceProvider(), [
            'statuspage.title' => 'TestPage',
            'statuspage.checker' => $this->app->protect(function($app, $statusChecker) use ($mock) {
                $statusChecker->addCheck($mock);
            })
        ]);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/status');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('th:contains("TestCheck")'));
        $this->assertCount(1, $crawler->filter('td:contains("OK")'));
    }

    public function testFailer()
    {
        $mock = $this->getMockBuilder(AbstractCheck::class)
            ->setConstructorArgs(array('TestCheck'))
            ->getMock();

        $result = new Result('TestCheck');
        $result->setSuccess(false);
        $result->setError('Failed');

        $mock->expects($this->once())
            ->method('check')
            ->willReturn($result);

        $this->app->register(new StatusPageServiceProvider(), array(
            'statuspage.title' => 'TestPage',
            'statuspage.checker' => $this->app->protect(function($app, $statusChecker) use ($mock) {
                $statusChecker->addCheck($mock);
            })
        ));

        $client = $this->createClient();
        $crawler = $client->request('GET', '/status');

        $this->assertFalse($client->getResponse()->isOk());
        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('th:contains("TestCheck")'));
        $this->assertCount(1, $crawler->filter('td:contains("Failed")'));
    }

    /**
     * DataProvider for testShowDetails.
     *
     * @return array
     */
    public function getTestShowDetails(): array
    {
        return array(
            array(false, false, 'System is up and running', 'my test detail'),
            array(true, false, 'System is having some issues', 'my test detail'),
            array(false, true, 'my test detail', 'System is up and running'),
            array(true, true, 'my test detail', 'System is having some issues'),
        );
    }

    /**
     * Checks if showing / hiding details works
     *
     * @dataProvider getTestShowDetails
     */
    public function testShowDetails($hasFailure, $showDetailsParam, $htmlContains, $htmlNotContains)
    {
        $checker = new StatusChecker();
        $check = new CallbackCheck('my test detail', function() use ($hasFailure) {
            return $hasFailure ? 'an error occured!' : true;
        });
        $checker->addCheck($check);
        $checker->check();

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../resources/views/');
        $twig = new Twig_Environment($loader, ['autoescape' => false]);
        $content = $twig->render(
            'status.twig',
            [
                'results' => $checker->getResults(),
                'title' => 'My test status page',
                'showDetails' => $showDetailsParam,
            ]
        );

        $this->assertContains($htmlContains, $content);
        $this->assertNotContains($htmlNotContains, $content);
    }
}
