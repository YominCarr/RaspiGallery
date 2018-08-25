<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 18:25
 */

require_once 'FileSystemEntity.php';

class Image extends FileSystemEntity
{
    private $type;
    private $width;
    private $height;
    private $creationData;

    public function __construct(string $name, string $fullPath, int $type, string $width, string $height, string $creationDate)
    {
        parent::__construct($name, $fullPath);
        $this->type = $type;
        $this->width = $width;
        $this->height = $height;
        $this->creationData = $creationDate;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getDisplayHTML(FileManager $fileManager): string
    {
        $src = $this->getRelativePath($fileManager);
        $src = $fileManager->pathToUrl($src);

        $alt = $this->getName();

        $str = "<img src=\"$src\" alt=\"$alt\">";
        return $str;
    }
}
