<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 21:36
 */

require_once __DIR__.'/FileManager.php';

class FileSystemEntity
{
    protected $name;
    protected $fullPath;

    public function __construct(string $name, string $fullPath)
    {
        $this->name = $name;
        $this->fullPath = $fullPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getRelativePath(FileManager $fileManager): string
    {
        return $fileManager->absoluteToRelativePath($this->getFullPath());
    }
}