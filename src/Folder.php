<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 18:19
 */

require_once 'FileSystemEntity.php';
require_once 'Image.php';

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
            $r = rand(1, $s);
            return $this->content["images"][$r];
        } else {
            return Image::getDummyImage($fileManager);
        }
    }

}
