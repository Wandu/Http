<?php
namespace Wandu\Http\Factory;

use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

trait HelperTrait
{
    /**
     * @param array $plainHeaders
     * @return array
     */
    protected function getPsrHeadersFromPlainHeader(array $plainHeaders)
    {
        $headers = [];
        foreach ($plainHeaders as $plainHeader) {
            list($key, $value) = array_map('trim', explode(':', $plainHeader, 2));
            $headers[strtolower($key)] = [$value];
        }
        return $headers;
    }

    /**
     * @param array $serverParams
     * @return array
     */
    protected function getPsrHeadersFromServerParams(array $serverParams)
    {
        $headers = array();
        foreach ($serverParams as $key => $value) {
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);
                $headers[$name] = $value;
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

    /**
     * @param string $uri
     * @param array $headers
     * @return \Psr\Http\Message\UriInterface
     */
    protected function makeUriObjectWithPsrHeaders($uri = '/', array $headers = [])
    {
        $plainUri = isset($headers['host'][0]) ? 'http://' . $headers['host'][0] : '';
        if ($uri !== '/' || ($uri === '/' && $plainUri === '')) {
            $plainUri .= $uri;
        }
        return new Uri($plainUri);
    }
}
