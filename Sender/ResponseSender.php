<?php
namespace Wandu\Http\Sender;

use Psr\Http\Message\ResponseInterface;
use Traversable;

class ResponseSender
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function sendToGlobal(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();
        $protocolVersion = $response->getProtocolVersion();

        header("HTTP/{$protocolVersion} $statusCode $reasonPhrase", true, $statusCode);
        foreach ($response->getHeaders() as $name => $values) {
            if (strtolower($name) === 'set-cookie') {
                foreach ($values as $cookie) {
                    header(sprintf('Set-Cookie: %s', $cookie), false);
                }
                break;
            }
            header(sprintf('%s: %s', $name, $response->getHeaderLine($name)));
        }
        $body = $response->getBody();
        if ($body) {
            // faster and less memory!
            if ($body instanceof Traversable) {
                foreach ($body as $contents) {
                    echo $contents;
                }
            } else {
                echo $body->__toString();
            }
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return string
     */
    public function parseToSocketBody(ResponseInterface $response)
    {
        $lines = [];

        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();
        $protocolVersion = $response->getProtocolVersion();

        $lines[] = "HTTP/{$protocolVersion} $statusCode $reasonPhrase";

        foreach ($response->getHeaders() as $name => $values) {
            if (strtolower($name) === 'set-cookie') {
                foreach ($values as $cookie) {
                    $lines[] = sprintf('Set-Cookie: %s', $cookie);
                }
                break;
            }
            $lines[] = sprintf('%s: %s', $name, $response->getHeaderLine($name));
        }

        return implode(" \r\n", $lines) . "\r\n\r\n" . $response->getBody()->__toString();
    }
}
