<?php
namespace Wandu\Http\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

class ServerRequestFactory
{
    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function fromGlobals()
    {
        return static::create($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES, 'php://input');
    }

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookies
     * @param array $files
     * @param string $bodyResource
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function create(
        array $server,
        array $get,
        array $post,
        array $cookies,
        array $files,
        $bodyResource
    ) {
        $body = new Stream($bodyResource);
        $headers = static::getHeadersFromServerParams($server);

        if (in_array('application/json', $headers['content-type'])) {
            $post = json_decode($body->__toString(), true);
        }
        return new ServerRequest(
            $server,
            $cookies,
            $get,
            UploadedFileFactory::fromFiles($files),
            $post,
            [],
            isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET',
            static::getUri($server),
            '1.1',
            $headers,
            $body
        );
    }

    /**
     * @param array $server
     * @return UriInterface
     */
    public static function getUri(array $server)
    {
        $stringUri = static::getHostAndPort($server);
        if ($stringUri !== '') {
            $stringUri = static::getScheme($server) . '://' . $stringUri;
        }
        $stringUri .= static::getRequestUri($server);
        return new Uri($stringUri);
    }

    /**
     * @param array $server
     * @return string
     */
    public static function getScheme(array $server)
    {
        if ((isset($server['HTTPS']) && $server['HTTPS'] !== 'off')
            || (isset($server['HTTP_X_FORWAREDED_PROTO']) && $server['HTTP_X_FORWAREDED_PROTO'] === 'https')) {
            return 'https';
        }
        return 'http';
    }

    /**
     * @param array $server
     * @return string
     */
    public static function getHostAndPort(array $server)
    {
        if (isset($server['HTTP_HOST'])) {
            return $server['HTTP_HOST'];
        }
        if (!isset($server['SERVER_NAME'])) {
            return '';
        }
        $host = $server['SERVER_NAME'];
        if (isset($server['SERVER_PORT'])) {
            $host .= ':' . $server['SERVER_PORT'];
        }
        return $host;
    }

    /**
     * @param array $server
     * @return string
     */
    public static function getRequestUri(array $server)
    {
        if (isset($server['REQUEST_URI'])) {
            return $server['REQUEST_URI'];
        }
        return '/';
    }

    /**
     * @param array $serverParams
     * @return array
     */
    protected function getHeadersFromServerParams(array $serverParams)
    {
        $headers = array();
        foreach ($serverParams as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE superglobal
                continue;
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
        }
        return $headers;
    }
}
