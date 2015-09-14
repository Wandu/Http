<?php
namespace Wandu\Http\Session;

use DateTime;
use DateInterval;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionAdapterInterface;

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
        $cookieName = $this->config['name'];
        if (!$cookieJar->has($cookieName)) {
            $cookieJar->set(
                $cookieName,
                $this->generateId(),
                (new DateTime())->setTimestamp(time() + $this->config['timeout'])
            );
        }
        return $this->adapter->read($cookieJar->get($cookieName));
    }

    /**
     * @return string
     */
    protected function generateId()
    {
        return sha1($this->config['name'] . uniqid());
    }
}
