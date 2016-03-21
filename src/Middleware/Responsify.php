<?php
namespace Wandu\Http\Middleware;

use Closure;
use Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Wandu\Http\Psr\Factory\ResponseFactory;

class Responsify
{
    /** @var \Wandu\Http\Psr\Factory\ResponseFactory */
    protected $factory;

    /**
     * @param \Wandu\Http\Psr\Factory\ResponseFactory $factory
     */
    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Closure $next
     * @return \Psr\Http\Message\ResponseInterface|string
     * @throws \RuntimeException
     */
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof ResponseInterface) {
            return $response;
        }
        if (!isset($response)) {
            $response = '';
        }
        while (is_callable($response)) {
            $nextResponse = call_user_func($response);
            if ($nextResponse instanceof Generator) {
                return $this->factory->generator($response);
            }
            $response = $nextResponse;
        }
        // int, float, boolean, string
        if (is_scalar($response)) {
            if ($response === true) {
                $response = 'true';
            } elseif ($response === false) {
                $response = 'false';
            }
            return $this->factory->create((string)$response);
        }
        if (is_array($response) || is_object($response)) {
            return $this->factory->json($response);
        }
        if (is_resource($response)) {
            if ('stream' === get_resource_type($response)) {
                $mode = stream_get_meta_data($response)['mode'];
                // @todo use Stream
                if (strpos($mode, 'r') !== false || strpos($mode, '+') !== false) {
                    $contents = '';
                    while (!feof($response)) {
                        $contents .= fread($response, 1024);
                    }
                    return $this->factory->create($contents);
                }
            }
        }
        throw new RuntimeException('Unsupported Type of Response.');
    }
}
