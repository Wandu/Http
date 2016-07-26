<?php
namespace Wandu\Http
{

    use Wandu\Http\Factory\ResponseFactory;

    /**
     * @return \Wandu\Http\Factory\ResponseFactory
     */
    function response()
    {
        static $factory;
        if (!isset($factory)) {
            $factory = new ResponseFactory();
        }
        return $factory;
    }
}
namespace Wandu\Http\Response
{
    use Closure;
    use Generator;
    use Psr\Http\Message\ServerRequestInterface;
    use Wandu\Http\Exception\BadRequestException;
    use function Wandu\Http\response;

    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function create($content = null, $status = 200, array $headers = [])
    {
        return response()->create($content, $status, $headers);
    }

    /**
     * @param \Closure $area
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function capture(Closure $area, $status = 200, array $headers = [])
    {
        return response()->capture($area, $status, $headers);
    }

    /**
     * @param  string|array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function json($data = [], $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers);
    }

    /**
     * @param  string  $file
     * @param  string  $name
     * @param  array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function download($file, $name = null, array $headers = [])
    {
        return response()->download($file, $name, $headers);
    }

    /**
     * @param string $path
     * @param array $queries
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function redirect($path, $queries = [], $status = 302, $headers = [])
    {
        $parsedQueries = [];
        foreach ($queries as $key => $value) {
            $parsedQueries[] = "{$key}=" . urlencode($value);
        }
        if (count($parsedQueries)) {
            $path .= '?' . implode('&', $parsedQueries);
        }
        return response()->redirect($path, $status, $headers);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Wandu\Http\Exception\BadRequestException
     */
    function back(ServerRequestInterface $request)
    {
        if ($request->hasHeader('referer')) {
            return redirect($request->getHeader('referer'));
        }
        throw new BadRequestException();
    }

    /**
     * @param \Generator $generator
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function generator(Generator $generator, $status = 200, array $headers = [])
    {
        return response()->generator($generator, $status, $headers);
    }
}
