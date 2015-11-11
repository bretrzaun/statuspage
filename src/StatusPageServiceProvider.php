<?php
namespace BretRZaun\StatusPage;

use Silex\Application;
use Silex\ServiceProviderInterface;

class StatusPageServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {

    }

    public function boot(Application $app)
    {
        $app->get('/status', function() use ($app) {

            $checker = new StatusChecker($app);
            if (isset($app['statuspage.checker'])) {
                $app['statuspage.checker']($app, $checker);
            }
            $checker->check();

            $code = $checker->hasErrors() ? 503 : 200;
            $app['twig.loader.filesystem']->addPath(__DIR__ . '/../resources/views/', 'statuspage');
            $content = $app['twig']->render(
                '@statuspage/status.twig',
                array(
                    'results' => $checker->getResults(),
                    'title' => $app['statuspage.title']
                )
            );

            $response = new \Symfony\Component\HttpFoundation\Response($content, $code);
            return $response;
        });
    }
}
