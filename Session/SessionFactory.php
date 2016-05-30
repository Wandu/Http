<?php
namespace Wandu\Http\Session;

use DateTime;
use SessionHandlerInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionInterface;

class SessionFactory
{
    /** @var \SessionHandlerInterface */
    protected $handler;

    /** @var bool */
    protected $reset = false;

    /** @var array */
    protected $config;

    /**
     * @param \SessionHandlerInterface $handler
     * @param array $config
     */
    public function __construct(SessionHandlerInterface $handler, array $config = [])
    {
        $this->handler = $handler;
        $this->config = $config + [
                'timeout' => 3600,
                'name' => 'WdSessId',
                'gc_frequency' => 100,
            ];
    }

    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     * @return \Wandu\Http\Contracts\SessionInterface
     */
    public function fromCookieJar(CookieJarInterface $cookieJar)
    {
        $sessionName = $this->config['name'];
        if ($cookieJar->has($sessionName)) {
            $sessionId = $cookieJar->get($sessionName);
        } else {
            $sessionId = $this->generateId();
        }
        $data = @unserialize($this->handler->read($sessionId));
        return new Session($sessionId, $data ? $data : []);
    }
    
    /**
     * @param \Wandu\Http\Contracts\SessionInterface $session
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     * @return \Wandu\Http\Contracts\SessionInterface $session
     */
    public function toCookieJar(SessionInterface $session, CookieJarInterface $cookieJar)
    {
        $sessionName = $this->config['name'];

        // save to handler
        $this->handler->write($session->getId(), serialize($session->getRawParams()));

        // apply to cookie-jar
        $cookieJar->set(
            $sessionName,
            $session->getId(),
            (new DateTime())->setTimestamp(time() + $this->config['timeout'])
        );
        
        // garbage collection
        $pick = rand(1, max(1, $this->config['gc_frequency']));
        if ($pick === 1) {
            $this->handler->gc($this->config['timeout']);
        }
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
