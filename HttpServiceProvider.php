<?php
namespace Wandu\Http;

use Predis\Client;
use SessionHandlerInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Http\Factory\ResponseFactory;
use Wandu\Http\Session\Configuration;
use Wandu\Http\Session\Handler\FileHandler;
use Wandu\Http\Session\Handler\GlobalHandler;
use Wandu\Http\Session\Handler\RedisHandler;

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
        $app->closure(Configuration::class, function (Config $config) {
            return new Configuration([
                'timeout' => $config->get('session.timeout', 3600),
                'name' => $config->get('session.name', ini_get('session.name') ?: 'WdSessId'),
                'gc_frequency' => $config->get('session.gc_frequency', 100),
            ]);
        });
        $app->closure(SessionHandlerInterface::class, function (Config $config, ContainerInterface $app) {
            switch ($config->get('session.type')) {
                case 'file':
                    return new FileHandler($config->get('session.path', 'cache/sessions'));
                case 'redis':
                    return new RedisHandler($app[Client::class], $config->get('session.timeout', 3600));
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
