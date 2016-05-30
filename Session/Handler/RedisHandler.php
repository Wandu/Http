<?php
namespace Wandu\Http\Session\Handler;

use Predis\Client;
use SessionHandlerInterface;

class RedisHandler implements SessionHandlerInterface
{
    /** @var \Predis\Client */
    private $client;

    /** @var int */
    private $expire;

    /**
     * @param \Predis\Client $client
     * @param int $expire
     */
    public function __construct(Client $client, $expire = 1800)
    {
        $this->client = $client;
        $this->expire = $expire;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->client->del("wandu.http.sess.{$sessionId}");
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if ($dataSet = $this->client->get("wandu.http.sess.{$sessionId}")) {
            return $dataSet;
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $dataSet)
    {
        $this->client->set("wandu.http.sess.{$sessionId}", $dataSet);
        $this->client->expire("wandu.http.sess.{$sessionId}", $this->expire);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }
}
