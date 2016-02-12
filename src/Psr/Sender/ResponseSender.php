<?php
namespace Wandu\Http\Psr\Sender;

use Psr\Http\Message\ResponseInterface;

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

        header("HTTP/{$protocolVersion} $statusCode $reasonPhrase");
        foreach ($response->getHeaders() as $name => $values) {
            if (strtolower($name) === 'set-cookie') {
                foreach ($values as $cookie) {
                    header(sprintf('Set-Cookie: %s', $cookie), false);
                }
                break;
            }
            header(sprintf('%s: %s', $name, $response->getHeaderLine($name)));
        }
        echo $response->getBody();
    }

    /**
     * @deprecated
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function send(ResponseInterface $response)
    {
        $this->sendToGlobal($response);
    }
}
