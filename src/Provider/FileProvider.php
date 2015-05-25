<?php
namespace Wandu\Session\Provider;

use Wandu\Session\ProviderInterface;

class FileProvider implements ProviderInterface
{
    /** @var string */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $sessionId
     * @return FileSession
     */
    public function getSession($sessionId)
    {
        return new FileSession($this->path, $sessionId);
    }
}
