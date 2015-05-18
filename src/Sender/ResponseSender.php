<?php
namespace Wandu\Http\Sender;

use Psr\Http\Message\ResponseInterface;

class ResponseSender
{
    public static function send(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();
        $protocolVersion = $response->getProtocolVersion();

        header("HTTP/{$protocolVersion} $statusCode $reasonPhrase");
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        echo $response->getBody();
    }
}
