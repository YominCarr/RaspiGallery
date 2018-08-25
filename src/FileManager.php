<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 16:44
 */

require_once 'Config.php';
require_once 'Folder.php';
require_once 'Image.php';

class FileManager
{
    public function scanDirRecursively(string $path): array
    {
        if (!is_dir($path)) {
            throw new \Exception('No such directory: ' . $path);
        }

        $folders = array();
        $images = array();
        $handle = opendir($path);
        while (false !== ($value = readdir($handle))) {
            if ($value != "." && $value != "..") {
                $contentPath = $this->concatPaths($path, $value);

                if (is_dir($contentPath) == true) {
                    $contentPath .= DIRECTORY_SEPARATOR;
                    $content = $this->scanDirRecursively($contentPath);

                    array_push($folders, new Folder($value, $contentPath, $content));
                } else {
                    list($width, $height, $type, $attr) = getimagesize($contentPath, $info);
                    if ($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_GIF)
                        continue;
                    $creationDate = filectime($contentPath);

                    // @todo Check directly for thumbnails
                    array_push($images, new Image($value, $contentPath, $type, $width, $height, $creationDate));
                }
            }
        }
        closedir($handle);
        return array("folders" => $folders, "images" => $images);
    }

    public function pathToUrl(string $path): string
    {
        return str_replace([".\\", "\\", "./"], ["", "/", ""], $path);
    }

    public function concatPaths(string $path1, string $path2): string
    {
        $path = $path1 . DIRECTORY_SEPARATOR . $path2;
        $path = $this->pathToDir($path);
        return $path;
    }

    public function pathToDir(string $path): string
    {
        $search = array("/", "\\", DIRECTORY_SEPARATOR . "." . DIRECTORY_SEPARATOR);
        $replace = array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $path = str_replace($search, $replace, $path);
        $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path); //clean duplicated separators
        return $path;
    }

    public function getAbsolutePhotoDir(): string
    {
        $path = $this->concatPaths($this->pathToDir($_SERVER['DOCUMENT_ROOT']), Config::documentRoot);
        $path = $this->concatPaths($path, Config::photoDir);
        return $path;
    }

    public function getAbsoluteThumbnailDir(): string
    {
        $path = $this->concatPaths($this->pathToDir($_SERVER['DOCUMENT_ROOT']), Config::documentRoot);
        $path = $this->concatPaths($path, Config::thumbnailDir);
        return $path;
    }

}