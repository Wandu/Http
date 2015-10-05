<?php
namespace Wandu\Http\Session\Storage;

use Predis\Client;
use Wandu\Http\Contracts\SessionAdapterInterface;

class RedisAdapterInterface implements SessionAdapterInterface
{
    /** @var Client */
    private $client;

    /** @var string */
    private $redisName;

    /** @var array */
    private $loadedDataSet = [];

    /**
     * @param Client $client
     * @param string $sessionId
     * @prarm string $prefix
     */
    public function __construct(Client $client, $sessionId, $prefix = '_WD_')
    {
        $this->client = $client;
        $this->redisName = $prefix . $sessionId;
        $dataSet = $client->get($this->redisName);
        if (isset($dataSet)) {
            $this->dataSet = $this->loadedDataSet = unserialize($dataSet);
        }
    }

    /**
     * @param string $sessionId
     * @return array
     */
    public function read($sessionId)
    {
        // TODO: Implement read() method.
    }

    /**
     * @param string $sessionId
     * @param array $data
     */
    public function write($sessionId, array $data)
    {
        // TODO: Implement write() method.
    }

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId)
    {
        // TODO: Implement destroy() method.
    }

    public function __destruct()
    {
        if ($this->dataSet != $this->loadedDataSet) {
            $this->client->set($this->redisName, serialize($this->dataSet));
        }
    }
}
