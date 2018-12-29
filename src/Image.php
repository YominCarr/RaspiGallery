<?php

require_once __DIR__ . '/FileSystemEntity.php';
require_once __DIR__ . '/ExifReader.php';

class Image extends FileSystemEntity
{
    static public $exifReader;

    private $type;
    private $width;
    private $height;
    private $creationDate;
    private $modificationDate;
    private $exifData;

    public function __construct(string $name, string $fullPath, int $type, string $width, string $height,
                                string $creationDate, string $modificationDate, ExifData $exifData)
    {
        parent::__construct($name, $fullPath);
        $this->type = $type;
        $this->width = $width;
        $this->height = $height;
        $this->creationDate = $creationDate;
        $this->modificationDate = $modificationDate;
        $this->exifData = $exifData;
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

    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function getModificationDate(): string
    {
        return $this->modificationDate;
    }

    public function getExifData(): ExifData
    {
        return $this->exifData;
    }

    public function getDisplayHTML(FileManager $fileManager, string $classes, string $alt = ""): string
    {
        $src = $this->getRelativePath($fileManager);
        $src = $fileManager->pathToUrl($src);

        if (empty($alt)) {
            $alt = $this->getName();
        }

        $str = "<img src='$src' alt='$alt' class='$classes'>";
        return $str;
    }

    public static function createImage(string $name, string $filePath): Image
    {
        list($width, $height, $type, $attr) = getimagesize($filePath, $info);
        if ($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_GIF) {
            return NULL;
        }
        $creationDate = filectime($filePath);
        $modificationDate = filemtime($filePath);
        $exif = Image::$exifReader->readExifDataFromImage($filePath);

        return new Image($name, $filePath, $type, $width, $height, $creationDate, $modificationDate, $exif);
    }

    public static function getDummyImage(FileManager $fileManager): Image
    {
        $name = "dummy.png";
        $filePath = $fileManager->concatPaths(Config::documentRoot, "img/dummy.png");
        return self::createImage($name, $filePath);
    }
}

// @todo how to make that private?
Image::$exifReader = new ExifReader();
