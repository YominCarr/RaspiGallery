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

    protected $dummy = false;
    protected $thumbnailCreationDummy = false;

    public function __construct(string $name, string $fullPath, int $type, string $width, string $height,
                                int $creationDate, int $modificationDate, ExifData $exifData)
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

    public function getCreationDate(): int
    {
        return $this->creationDate;
    }

    public function getModificationDate(): int
    {
        return $this->modificationDate;
    }

    public function getExifData(): ExifData
    {
        return $this->exifData;
    }

    public function isDummy(): bool
    {
        return $this->dummy;
    }

    public function isThumbnailCreationDummy(): bool
    {
        return $this->thumbnailCreationDummy;
    }

    public function isValidImage(): bool
    {
        return true;
    }

    public function getDisplayHTML(FileManager $fileManager, string $classes, string $id = "", string $alt = "",
                                   array $additionalAttributes = []): string
    {
        $src = $this->getRelativePathAsUrl($fileManager);

        if (empty($alt)) {
            $alt = $this->getName();
        }

        $additional = "";
        foreach ($additionalAttributes as $key => $val) {
            $additional .= "$key='$val' ";
        }

        $str = "<img src='$src' alt='$alt' class='$classes' id='$id' $additional>";
        return $str;
    }

    public static function createImage(string $name, string $filePath): Image
    {
        list($width, $height, $type, $attr) = getimagesize($filePath, $info);
        if ($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_GIF) {
            return new InvalidImage($name, $filePath, $type == NULL ? -1 : $type);
        }
        $creationDate = filectime($filePath);
        $modificationDate = filemtime($filePath);
        $exif = Image::$exifReader->readExifDataFromImage($filePath);

        $originalImage = new Image($name, $filePath, $type, $width, $height, $creationDate, $modificationDate, $exif);
        if ($name === "IMG_4642.jpg") {
            $resizedImage = self::createResizedImage(new FileManager(), $originalImage);
            return $resizedImage;
        } else {
            return $originalImage;
        }
    }

    // TODO copy from createThumbnail
    private static function createResizedImage(FileManager $fileManager, Image $image): Image
    {
        ini_set('memory_limit', '96M');

        $pathToImage = $image->getFullPath();
        $width = $image->getWidth();
        $height = $image->getHeight();
        $type = $image->getType();

        $horizontalScale = Config::resizeWidth * 1.0 / $width;
        $verticalScale = Config::resizeHeight * 1.0 / $height;
        $scale = ($horizontalScale <= $verticalScale ? $horizontalScale : $verticalScale);

        $scale = min($scale, 1.0); //Only scale down
        $newWidth = $width * $scale;
        $newHeight = $height * $scale;

        if ($type == IMAGETYPE_JPEG) {
            $srcImage = imagecreatefromjpeg($pathToImage);
        } else if ($type == IMAGETYPE_GIF) {
            $srcImage = imagecreatefromgif($pathToImage);
        } else if ($type == IMAGETYPE_PNG) {
            $srcImage = imagecreatefrompng($pathToImage);
        }
        if (!$srcImage) {
            throw new \Exception('Could not read image file: ' . $pathToImage);
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);

            imagedestroy($white);
        }

        if (Config::thumbnailResampleInsteadResize) {
            imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        } else {
            imagecopyresized($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        }

        $resizedImagePath = Image::getPathForResizedImage($image);
        $fileManager->createFoldersOfPath($fileManager->removeFileFromPath($resizedImagePath));
        imagejpeg($resizedImage, $resizedImagePath, Config::resizeJPEGQuality);

        imagedestroy($resizedImage);
        imagedestroy($srcImage);

        $resizedImage = new Image($image->getName(), $resizedImagePath, $type, $newWidth, $newHeight, time(), time(), new ExifData());
        return $resizedImage;
    }

    // TODO copy from createThumbnail
    public static function getPathForResizedImage(Image $image): string
    {
        $path = $image->getFullPath();
        $path = str_replace(Config::photoDir, Config::tempDir, $path);
        return $path;
    }

    public static function getDummyImage(FileManager $fileManager): Image
    {
        return Image::getGenericDummyImage($fileManager, "dummy.png");
    }

    public static function getNoImagesDummyImage(FileManager $fileManager): Image
    {
        return Image::getGenericDummyImage($fileManager, "noImages.png");
    }

    public static function getThumbnailsGenerationDummyImage(FileManager $fileManager): Image
    {
        return Image::getGenericDummyImage($fileManager, "thumbnailCreation.png", true);
    }

    private static function getGenericDummyImage(FileManager $fileManager, string $name, bool $thumbnailCreationDummy = false): Image
    {
        $filePath = $fileManager->concatPaths($fileManager->pathToDir($_SERVER['DOCUMENT_ROOT']), Config::documentRoot);
        $filePath = $fileManager->concatPaths($filePath, "img/" . $name);

        $image = self::createImage($name, $filePath);
        $image->dummy = true;
        $image->thumbnailCreationDummy = $thumbnailCreationDummy;

        return $image;
    }

    public static function getTextboxFakeImage(string $name): Image
    {
        return new FakeTextBoxImage($name);
    }
}

class InvalidImage extends Image
{

    public function __construct(string $name, string $fullPath, int $type)
    {
        parent::__construct($name, $fullPath, $type, "0","0", "0", "0", new ExifData());
        $this->dummy = true;
        $this->thumbnailCreationDummy = false;
    }

    public function isValidImage(): bool
    {
        return false;
    }

}

class FakeTextBoxImage extends Image
{

    public function __construct(string $name)
    {
        parent::__construct($name, $name, IMAGETYPE_UNKNOWN, "0","0", "0", "0", new ExifData());
        $this->dummy = true;
        $this->thumbnailCreationDummy = false;
    }

    public function isValidImage(): bool
    {
        return false;
    }

    public function getDisplayHTML(FileManager $fileManager, string $classes, string $id = "", string $alt = "",
                                   array $additionalAttributes = []): string
    {
        return "<div class='fakeImageTextBox'><div class='button blue'>$this->name</div></div>";
    }
}

// @todo how to make that private?
Image::$exifReader = new ExifReader();
