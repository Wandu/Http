<?php
namespace Wandu\Http;

use Predis\Client;
use SessionHandlerInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Http\Factory\ResponseFactory;
use Wandu\Http\Session\Configuration;
use Wandu\Http\Session\Handler\FileHandler;
use Wandu\Http\Session\Handler\GlobalHandler;
use Wandu\Http\Session\Handler\RedisHandler;
use function Wandu\Foundation\config;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(ResponseFactory::class, function () {
            return response(); // singleton
        });
        $app->closure(Configuration::class, function () {
            return new Configuration([
                'timeout' => config('session.timeout', 3600),
                'name' => config('session.name', ini_get('session.name') ?: 'WdSessId'),
                'gc_frequency' => config('session.gc_frequency', 100),
            ]);
        });
        $app->closure(SessionHandlerInterface::class, function (ContainerInterface $app) {
            switch (config('session.type')) {
                case 'file':
                    return new FileHandler(config('session.path', 'cache/sessions'));
                case 'redis':
                    return new RedisHandler($app[Client::class], config('session.timeout', 3600));
                default:
                    return new GlobalHandler();
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
