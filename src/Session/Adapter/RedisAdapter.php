<?php
namespace Wandu\Http\Session\Adapter;

use Predis\Client;
use Wandu\Http\Contracts\SessionAdapterInterface;

class RedisAdapter implements SessionAdapterInterface
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
            return unserialize($dataSet);
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, array $dataSet)
    {
        $this->client->set("wandu.http.sess.{$sessionId}", serialize($dataSet));
        $this->client->expire("wandu.http.sess.{$sessionId}", $this->expire);
    }
}
