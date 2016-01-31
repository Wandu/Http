<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    const OK = 0;
    const ERR_MAX_INI_SIZE = 1;
    const ERR_MAX_FORM_SIZE = 2;
    const ERR_UPLOAD_PARTIAL = 3;
    const ERR_NO_FILE = 4;
    // 5
    const ERR_NO_TEMP_DIR = 6;
    const ERR_CANNOT_WRITE = 7;
    const ERR_EXTENSION = 8;

    /** @var string */
    protected $file;

    /** @var int */
    protected $size;

    /** @var int */
    protected $error;

    /** @var string */
    protected $clientFileName;

    /** @var string */
    protected $clientMediaType;

    /** @var bool */
    protected $moved = false;

    /**
     * @param string $file
     * @param int $size
     * @param int $error
     * @param string $clientFileName
     * @param string $clientMediaType
     */
    public function __construct(
        $file = null,
        $size = null,
        $error = null,
        $clientFileName = null,
        $clientMediaType = null
    ) {
        $this->file = $file;
        $this->size = isset($size) ? (int) $size : null;
        $this->error = isset($error) ? (int) $error : null;
        $this->clientFileName = $clientFileName;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved.');
        }
        return new Stream($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath)
    {
        if (!is_string($targetPath) || $targetPath === '') {
            throw new InvalidArgumentException('Invalid path provided for move operation. It must be a string.');
        }
        if ($this->moved) {
            throw new RuntimeException('Cannot move the file. Already moved!');
        }
        if (\PHP_SAPI === '' || 0 === strpos(\PHP_SAPI, 'cli')) {
            $result = rename($this->file, $targetPath);
        } else {
            $result = move_uploaded_file($this->file, $targetPath);
        }
        if (false === $result) {
            throw new RuntimeException('Error occurred while moving uploaded file.');
        }
        $this->moved = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename()
    {
        return $this->clientFileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}
