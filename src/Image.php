<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 18:25
 */

class Image
{
    private $name;
    private $fullPath;
    private $width;
    private $height;
    private $creationData;

    public function __construct(string $name, string $fullPath, string $width, string $height, string $creationDate)
    {
        $this->name = $name;
        $this->fullPath = $fullPath;
        $this->width = $width;
        $this->height = $height;
        $this->creationData = $creationDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }
}
