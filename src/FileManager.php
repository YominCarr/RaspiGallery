<?php

require_once __DIR__ . '/IncludeHeader.php';
require_once __DIR__ . '/Folder.php';
require_once __DIR__ . '/Image.php';
require_once __DIR__ . '/ImageSorter.php';

class FileManager
{

    private $folderSorter;
    private $imageSorter;

    public function __construct() {
        $this->folderSorter = new FileSystemEntitySorter();
        $this->imageSorter = new ImageSorter();
    }

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
                    $image = Image::createImage($value, $contentPath);
                    if ($image->isValidImage()) {
                        array_push($images, $image);
                    }
                }
            }
        }

        closedir($handle);

        $this->sortFolders($folders);
        $this->sortImages($images);

        return array("folders" => $folders, "images" => $images);
    }

    public function getShallowSubfolderList(string $path): array
    {
        if (!is_dir($path)) {
            throw new \Exception('No such directory: ' . $path);
        }

        $folders = array();
        $handle = opendir($path);

        while (false !== ($value = readdir($handle))) {
            if ($value != "." && $value != "..") {
                $contentPath = $this->concatPaths($path, $value);

                if (is_dir($contentPath) == true) {
                    $contentPath .= DIRECTORY_SEPARATOR;
                    array_push($folders, new Folder($value, $contentPath, []));
                }
            }
        }

        closedir($handle);

        $this->sortFolders($folders);

        return $folders;
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

    public function absoluteToRelativePath(string $path): string
    {
        $path = str_replace($this->pathToDir($_SERVER['DOCUMENT_ROOT']), "", $path);
        if (substr($path, 0, 1) !== DIRECTORY_SEPARATOR) {
            $path = DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    public function createFoldersOfPath(string $path)
    {
        if (file_exists($path) && is_dir($path)) {
            return;
        }
        mkdir($path, 0744, true);
    }

    public function removeFileFromPath(string $path): string
    {
        return dirname($path);
    }

    public function cleanExtraSeparatorsFromPath(string $path): string
    {
        $newPath = ltrim($path, DIRECTORY_SEPARATOR);
        $newPath = preg_replace('#' . DIRECTORY_SEPARATOR . '+#', DIRECTORY_SEPARATOR, $newPath);
        return $newPath;
    }

    public function trimTrailingDirSeparator(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    private function sortFolders(array &$folders)
    {
        $this->folderSorter->sort($folders);
    }

    private function sortImages(array &$images)
    {
        $this->imageSorter->sort($images);
    }

}