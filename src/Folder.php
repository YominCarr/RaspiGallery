<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 18:19
 */

require_once __DIR__.'/FileSystemEntity.php';
require_once __DIR__.'/Image.php';

class Folder extends FileSystemEntity
{

    private $content;

    public function __construct(string $name, string $fullPath, array $content)
    {
        parent::__construct($name, $fullPath);
        $this->content = $content;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getRandomImage(FileManager $fileManager): Image
    {
        $s = sizeof($this->content["images"]);
        if ($s > 0) {
            $r = rand(0, $s-1);
            return $this->content["images"][$r];
        } else {
            return Image::getDummyImage($fileManager);
        }
    }

    public function getFirstImage(FileManager $fileManager): Image
    {
        if (sizeof($this->content["images"]) > 0) {
            return $this->content["images"][0];
        } else {
            return Image::getDummyImage($fileManager);
        }
    }

}
