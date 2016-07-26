<?php
namespace Wandu\Http\Support;

use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Exception\NotFoundException;
use Wandu\Http\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Sender\ResponseSender;

class HttpServer
{
    /** @var \Wandu\Http\Factory\ServerRequestFactory */
    protected $requestFactory;

    /** @var \Wandu\Http\Psr\Sender\ResponseSender */
    protected $responseSender;

    public function __construct(ServerRequestFactory $requestFactory, ResponseSender $responseSender)
    {
        $this->requestFactory = $requestFactory;
        $this->responseSender = $responseSender;
    }

    /**
     * @param string $host
     * @param int $port
     * @param callable $handler
     * @throws \Exception
     */
    public function listen($host, $port, callable $handler)
    {
        // create a socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);

        // bind the socket
        if (!socket_bind($socket, $host, (int)$port)) {
            throw new \Exception(
                "Could not bind: {$host}:{$port} - " .
                socket_strerror(socket_last_error())
            );
        }

        while (1) {
            // listen for connections
            socket_listen($socket);

            // try to get the client socket resource
            // if false we got an error close the connection and continue
            if (!$client = socket_accept($socket)) {
                socket_close($client);
                continue;
            }

            // create new request instance with the clients header.
            // In the real world of course you cannot just fix the max size to 1024..
            $contents = socket_read($client, 1024);

            echo $contents;

            // execute the callback
            $response = call_user_func($handler, $this->requestFactory->fromSocketBody($contents));

            // check if we really recived an Response object
            // if not return a 404 response object
            if (!$response || !$response instanceof ResponseInterface) {
                $response = new NotFoundException();
            }

            // make a string out of our response
            $responseBody = $this->responseSender->parseToSocketBody($response);

            // write the response to the client socket
            socket_write($client, $responseBody, strlen($responseBody));

            // close the connetion so we can accept new ones
            socket_close($client);
        }
    }
}
