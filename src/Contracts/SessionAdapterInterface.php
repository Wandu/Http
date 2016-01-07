<?php
namespace Wandu\Http\Contracts;

interface SessionAdapterInterface
{
    /**
     * @param string $sessionId
     * @return array
     */
    public function read($sessionId);

    /**
     * @param string $sessionId
     * @param array $dataSet
     */
    public function write($sessionId, array $dataSet);

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId);
}
