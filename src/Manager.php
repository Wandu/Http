<?php
namespace Wandu\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Manager
{
    /** @var StorageAdapterInterface */
    protected $handler;

    /** @var bool */
    protected $reset = false;

    /** @var array */
    protected $config;

    /**
     * @param \Wandu\Session\StorageAdapterInterface $handler
     * @param array $config
     */
    public function __construct(StorageAdapterInterface $handler, array $config = [])
    {
        $this->handler = $handler;
        $this->config = $config + [
                'timeout' => 300,
                'name' => 'WdSessId',
            ];
    }

    /**
     * @param ServerRequestInterface $request
     * @return Storage
     */
    public function readFromRequest(ServerRequestInterface $request)
    {
        $cookies = $request->getCookieParams();
        if (isset($cookies[$this->config['name']])) {
            $this->sessionId = $cookies[$this->config['name']];
        } else {
            $this->sessionId = $this->generateId();
            $this->reset = true;
        }
        return new Storage($this->sessionId, $this->handler->read($this->sessionId));
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
