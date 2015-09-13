<?php
namespace Wandu\Http\Session\Storage;

use Wandu\Http\Session\DataSet;
use Wandu\Http\Session\DataSetInterface;
use Wandu\Http\Session\StorageAdapterInterface;

class FileAdapter implements StorageAdapterInterface
{
    /** @var string */
    private $fileRoot;

    /**
     * @param string $fileRoot
     */
    public function __construct($fileRoot)
    {
        $this->fileRoot = $fileRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        unlink($this->fileRoot . '/' . $sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $path = $this->fileRoot . '/' . $sessionId;
        return DataSet::fromArray(file_exists($path) ? unserialize(file_get_contents($path)) : []);
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, DataSetInterface $dataSet)
    {
        $path = $this->fileRoot . '/' . $sessionId;
        file_put_contents(
            $path,
            serialize($dataSet->toArray())
        );
    }
}
