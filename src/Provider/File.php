<?php
namespace Wandu\Session\Provider;

use Wandu\Session\ProviderInterface;

class File implements ProviderInterface
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

    public function getSession($id)
    {
        return new FileSession($this->path, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        // TODO: Implement remove() method.
    }
}
