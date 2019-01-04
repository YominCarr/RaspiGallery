<?php

require_once __DIR__ . '/FileManager.php';

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

    public function getRelativePathAsUrl(FileManager $fileManager): string
    {
        $path = $this->getRelativePath($fileManager);
        return $fileManager->pathToUrl($path);
    }

    public function getRelativePathToPhotoDir(FileManager $fileManager): string
    {
        $relativePath = $this->getRelativePath($fileManager);
        $relativePath = str_replace($fileManager->concatPaths(Config::documentRoot, Config::photoDir), '', $relativePath);
        $relativePath = $fileManager->cleanExtraSeparatorsFromPath($relativePath);
        return $relativePath;
    }
}