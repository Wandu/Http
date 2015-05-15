<?php
namespace Wandu\Http\Factory;

use Wandu\Http\ServerRequest;
use Wandu\Http\Uri;

class ServerRequestFactory
{
    public static function fromGlobals(array $server, array $get, array $post, array $cookies, array $files)
    {
        return new ServerRequest(
            $server,
            $cookies,
            $get,
            UploadedFileFactory::fromFiles($files),
            $post,
            [],
            '1.1',
            static::getUri($server)
        );
    }

    /**
     * @param array $server
     * @return Uri
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
            $host .= ':'. $server['SERVER_PORT'];
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
}
