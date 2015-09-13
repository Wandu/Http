<?php
namespace Wandu\Session;

interface StorageAdapterInterface
{
    /**
     * @param string $sessionId
     * @return \Wandu\Session\DataSetInterface $dataSet
     */
    public function read($sessionId);

    /**
     * @param $sessionId
     * @param \Wandu\Session\DataSetInterface $dataSet
     */
    public function write($sessionId, DataSetInterface $dataSet);

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId);
}
