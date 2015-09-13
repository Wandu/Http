<?php
namespace Wandu\Http\Session;

interface StorageAdapterInterface
{
    /**
     * @param string $sessionId
     * @return \Wandu\Http\Session\DataSetInterface $dataSet
     */
    public function read($sessionId);

    /**
     * @param $sessionId
     * @param \Wandu\Http\Session\DataSetInterface $dataSet
     */
    public function write($sessionId, DataSetInterface $dataSet);

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId);
}
