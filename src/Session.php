<?php
namespace Wandu\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Session
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $id;

    /** @var int */
    protected $timeout;

    /** @var SessionInterface */
    protected $session;

    /** @var bool */
    protected $reset = false;

    /**
     * @param string $name
     * @param ServerRequestInterface $request
     * @param ProviderInterface $provider
     * @param int $timeout
     */
    public function __construct($name, ServerRequestInterface $request, ProviderInterface $provider, $timeout = 300)
    {
        $this->name = $name;
        $cookies = $request->getCookieParams();
        if (!isset($cookies[$name])) {
            $this->reset();
        } else {
            $this->id = $cookies[$name];
        }
        $this->session = $provider->getSession($this->id);
        $this->timeout = $timeout;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->session->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function set($name, $value)
    {
        $this->session->set($name, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return self
     */
    public function reset()
    {
        $this->id = sha1(uniqid());
        $this->reset = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReset()
    {
        return $this->reset;
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function applyResponse(ResponseInterface $response)
    {
        if ($this->reset) {
            return $response->withHeader('Set-Cookie', "{$this->name}={$this->id}");
        }
        return $response;
    }
}
