<?php
namespace Wandu\Http;

use Predis\Client;
use Psr\Http\Message\ServerRequestInterface;
use SessionHandlerInterface;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\ServerParamsInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Factory\ResponseFactory;
use Wandu\Http\Parameters\CookieJar;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
use Wandu\Http\Parameters\ServerParams;
use Wandu\Http\Parameters\Session;
use Wandu\Http\Psr\ServerRequest;
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
        $app->closure(Configuration::class, function (ConfigInterface $config) {
            return new Configuration([
                'timeout' => $config->get('session.timeout', 3600),
                'name' => $config->get('session.name', ini_get('session.name') ?: 'WdSessId'),
                'gc_frequency' => $config->get('session.gc_frequency', 100),
            ]);
        });
        $app->closure(SessionHandlerInterface::class, function (ConfigInterface $config, ContainerInterface $app) {
            switch ($config->get('session.type')) {
                case 'file':
                    return new FileHandler($config->get('session.path', 'cache/sessions'));
                case 'redis':
                    return new RedisHandler($app[Client::class], $config->get('session.timeout', 3600));
                default:
                    return new GlobalHandler();
            }
        });

        $app->alias(ServerRequestInterface::class, ServerRequest::class);
        $app->alias('request', ServerRequest::class);

        $app->alias(ServerParamsInterface::class, ServerParams::class);
        $app->alias('server_params', ServerParams::class);

        $app->alias(QueryParamsInterface::class, QueryParams::class);
        $app->alias('query_params', QueryParams::class);

        $app->alias(ParsedBodyInterface::class, ParsedBody::class);
        $app->alias('parsed_body', ParsedBody::class);

        $app->closure(CookieJar::class, function (ServerRequestInterface $request) {
            return $request->getAttribute('cookie');
        });
        $app->alias(CookieJarInterface::class, CookieJar::class);
        $app->alias('cookie', CookieJar::class);

        $app->closure(Session::class, function (ServerRequestInterface $request) {
            return $request->getAttribute('session');
        });
        $app->alias(SessionInterface::class, Session::class);
        $app->alias('session', Session::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
