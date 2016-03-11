<?php
namespace Wandu\Http\Session;

use DateTime;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionAdapterInterface;
use Wandu\Http\Contracts\SessionInterface;

class SessionFactory
{
    /** @var \Wandu\Http\Contracts\SessionAdapterInterface */
    protected $adapter;

    /** @var bool */
    protected $reset = false;

    /** @var array */
    protected $config;

    /**
     * @param \Wandu\Http\Contracts\SessionAdapterInterface $adapter
     * @param array $config
     */
    public function __construct(SessionAdapterInterface $adapter, array $config = [])
    {
        $this->adapter = $adapter;
        $this->config = $config + [
                'timeout' => 3600,
                'name' => 'WdSessId',
            ];
    }

    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     * @return \Wandu\Http\Contracts\SessionInterface
     */
    public function fromCookieJar(CookieJarInterface $cookieJar)
    {
        $sessionName = $this->config['name'];
        if (!$cookieJar->has($sessionName)) {
            $sessionId = $this->generateId();
            return new Session($sessionId, $this->adapter->read($sessionId));
        }
        $sessionId = $cookieJar->get($sessionName);
        return new Session($sessionId, $this->adapter->read($sessionId));
    }

    /**
     * @param \Wandu\Http\Contracts\SessionInterface $session
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     * @return \Wandu\Http\Contracts\SessionInterface $session
     */
    public function toCookieJar(SessionInterface $session, CookieJarInterface $cookieJar)
    {
        $sessionName = $this->config['name'];
        $this->adapter->write($session->getId(), $session->getRawParams());
        if (!$cookieJar->has($sessionName)) {
            $sessionId = $this->generateId();
        } else {
            $sessionId = $cookieJar->get($sessionName);
        }
        $cookieJar->set(
            $sessionName,
            $sessionId,
            (new DateTime())->setTimestamp(time() + $this->config['timeout'])
        );
        return $session;
    }

    /**
     * @return string
     */
    protected function generateId()
    {
        return sha1($this->config['name'] . uniqid());
    }
}
