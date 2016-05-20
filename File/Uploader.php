<?php
namespace Wandu\Http\File;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Wandu\Http\Psr\UploadedFile;

class Uploader
{
    /**
     * @param string $basePath
     * @param bool $createBasePath
     */
    public function __construct($basePath, $createBasePath = false)
    {
        if ($createBasePath && !is_dir($basePath) && !file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }
        if (!is_dir($basePath)) {
            throw new InvalidArgumentException("the base path is not a directory!");
        }
        if (!is_writable($basePath)) {
            throw new InvalidArgumentException("this base path can not be written!");
        }
        $this->basePath = $basePath;
    }

    /**
     * @param \Psr\Http\Message\UploadedFileInterface[] $files
     * @return array
     */
    public function uploadFiles(array $files)
    {
        $arrayToReturn = [];
        foreach ($files as $name => $file) {
            if ($file instanceof UploadedFile) {
                $result = $this->uploadFile($file);
                if ($result) {
                    $arrayToReturn[$name] = $result;
                }
            } elseif (is_array($file)) {
                $result = $this->uploadFiles($file);
                if (count($result)) {
                    $arrayToReturn[$name] = $result;
                }
            } else {
                throw new InvalidArgumentException("unknown type of file!");
            }
        }
        return $arrayToReturn;
    }

    /**
     * @param \Psr\Http\Message\UploadedFileInterface $file
     * @return string
     */
    public function uploadFile(UploadedFileInterface $file)
    {
        if ($file->getError() === UploadedFile::OK) {
            $fileName = pathinfo($file->getClientFilename());
            $dir = date("ymd");
            if (!file_exists("{$this->basePath}/{$dir}")) {
                if (false === @mkdir("{$this->basePath}/{$dir}", 0777, true)) {
                    throw new RuntimeException("fail to create directory.");
                }
            }
            do {
                $newFileName = sha1($fileName['filename'] . rand());
                $newFilePath = "{$this->basePath}/{$dir}/{$newFileName}.{$fileName['extension']}";
            } while (file_exists($newFilePath));

            $file->moveTo($newFilePath);
            return "{$dir}/{$newFileName}.{$fileName['extension']}";
        }
    }
}
