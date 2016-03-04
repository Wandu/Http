<?php
namespace Wandu\Http\Psr\Factory;

use Wandu\Http\Psr\UploadedFile;

class UploadedFileFactory
{
    /**
     * @deprecated
     * @param array $files
     * @return array
     */
    public function fromFiles(array $files)
    {
        return $this->createFromFiles($files);
    }

    /**
     * @param array $files
     * @return array
     */
    public function createFromFiles(array $files)
    {
        $uploadedFiles = [];
        foreach ($files as $name => $file) {
            $uploadedFiles[$name] = $this->factoryFilesTree(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );
        }
        return $uploadedFiles;
    }

    /**
     * @param array|string $fileDatas
     * @param array|int $sizeDatas
     * @param array|int $errorDatas
     * @param array|string $nameDatas
     * @param array|string $typeDatas
     * @return array
     */
    protected function factoryFilesTree($fileDatas, $sizeDatas, $errorDatas, $nameDatas, $typeDatas)
    {
        if (!is_array($errorDatas)) {
            return new UploadedFile($fileDatas, $sizeDatas, $errorDatas, $nameDatas, $typeDatas);
        }
        $filesTree = [];
        foreach ($errorDatas as $name => $errorData) {
            $filesTree[$name] = $this->factoryFilesTree(
                $fileDatas[$name],
                $sizeDatas[$name],
                $errorData,
                $nameDatas[$name],
                $typeDatas[$name]
            );
        }
        return $filesTree;
    }
}
