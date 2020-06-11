<?php
namespace Amplifier\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Amplifier\AmplifierService;

class AmplifierServiceProvider implements
    ServiceProviderInterface,
    EventListenerProviderInterface
{
    public function register(Container $app)
    {
        // Wrap any existing dispatcher
        $app->extend(
            'dispatcher',
            function (
                $dispatcher,
                Application $app
            ) {
                return new \Amplifier\AmplifierEventDispatcher(
                    $dispatcher,
                    $app['amplifier.service']
                );
            }
        );

        $app['amplifier.service'] = function ($app) {
            // if (!isset($app['amplifier.dsn'])) {
            //     throw new RuntimeException(
            //         'You must set the "amplifier.dsn" container parameter in order to use the AmplifierServiceProvider.'
            //     );
            // }
            return new AmplifierService();
        };
    }
    /**
     * Subscribe SentryService.
     *
     * {@inheritdoc}
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['amplifier.service']);
    }
}
