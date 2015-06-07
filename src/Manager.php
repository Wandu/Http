<?php
namespace Wandu\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Manager
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $id;

    /** @var SessionHandlerInterface */
    protected $handler;

    /** @var bool */
    protected $reset = false;

    /** @var int */
    protected $timeout;

    /**
     * @param string $name
     * @param SessionHandlerInterface $handler
     * @param int $timeout
     */
    public function __construct($name, SessionHandlerInterface $handler, $timeout = 300)
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->timeout = $timeout;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Storage
     */
    public function readFromRequest(ServerRequestInterface $request)
    {
        $cookies = $request->getCookieParams();
        if (isset($cookies[$this->name])) {
            $this->id = $cookies[$this->name];
        } else {
            $this->id = $this->generateId();
            $this->reset = true;
        }
        return new Storage($this->handler->read($this->id));
    }

    /**
     * @param ResponseInterface $response
     * @param Storage $storage
     * @return ResponseInterface
     */
    public function writeToResponse(ResponseInterface $response, Storage $storage)
    {
        $this->handler->write($this->id, $storage->toArray());
        if ($this->reset) {
            return $response->withHeader('Set-Cookie', "{$this->name}={$this->id}");
        }
        return $response;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isReset()
    {
        return $this->reset;
    }


    /**
     * @return string
     */
    protected function generateId()
    {
        return sha1(uniqid());
    }
}
