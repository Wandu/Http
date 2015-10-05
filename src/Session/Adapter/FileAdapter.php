<?php
namespace Wandu\Http\Session\Adapter;

use Wandu\Http\Contracts\SessionAdapterInterface;

class FileAdapter implements SessionAdapterInterface
{
    /** @var string */
    private $fileRoot;

    /** @var int */
    private $expire;

    /**
     * @param string $fileRoot
     * @param int $expire
     */
    public function __construct($fileRoot, $expire = 1800)
    {
        $this->fileRoot = $fileRoot;
        $this->expire = $expire;
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
        if (file_exists($path)) {
            $dataSet = unserialize(file_get_contents($path));
            if (isset($dataSet['expire']) && $dataSet['expire'] > time()) {
                return $dataSet['dataset'];
            }
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, array $dataSet)
    {
        $path = $this->fileRoot . '/' . $sessionId;
        file_put_contents(
            $path,
            serialize([
                'expire' => time() + $this->expire,
                'dataset' => $dataSet,
            ])
        );
    }
}
