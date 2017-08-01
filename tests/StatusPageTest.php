<?php
namespace BretRZaun\StatusPage\Tests;

use BretRZaun\StatusPage\Check\AbstractCheck;
use BretRZaun\StatusPage\Result;
use BretRZaun\StatusPage\StatusPageServiceProvider;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\WebTestCase;

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
}
