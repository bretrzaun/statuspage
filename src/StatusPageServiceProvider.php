<?php
namespace BretRZaun\StatusPage;

use Pimple\Container;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated
 */
class StatusPageServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $container)
    {

    }

    public function boot(Application $app)
    {
        $app->get('/status', function() use ($app) {

            $checker = new StatusChecker();
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

            return new Response($content, $code);
        });
    }
}
