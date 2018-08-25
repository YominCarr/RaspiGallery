<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 18:19
 */

class Folder
{

    private $name;
    private $fullPath;
    private $content;

    public function __construct(string $name, string $fullPath, array $content)
    {
        $this->name = $name;
        $this->fullPath = $fullPath;
        $this->content = $content;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getContent(): string
    {
        return $this->content;
    }

}
