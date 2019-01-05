<?php

require_once __DIR__ . '/FileManager.php';
require_once __DIR__ . '/ExifData.php';

// @todo Need a mechanism to remove old thumbnails from disk, maybe offer a file for cron and also for periodic calling
// @todo Thumbnails should be generated asynchronously and dummy images should be in place and periodicly be replaced then
class ThumbnailManager
{

    private $thumbnailRequestBuffer = [];

    public function getThumbnailOrDummyAndBufferRequest(FileManager $fileManager, Image $image): Image
    {
        if ($this->thumbnailExists($image)) {
            $thumbnailPath = $this->getThumbnailPathForImage($image);
            $thumbnail = Image::createImage($image->getName(), $thumbnailPath);
            return $thumbnail;
        } else {
            $this->addThumbnailCreationRequestToBuffer($image->getName(), $image->getFullPath());
            return Image::getDummyImage($fileManager, $image->getName());
        }
    }

    public function generateThumbnailIfNeeded(FileManager $fileManager, Image $image): Image
    {
        if (!$this->thumbnailExists($image)) {
            return $this->createThumbnail($fileManager, $image);
        }

        $thumbnailPath = $this->getThumbnailPathForImage($image);
        $thumbnail = Image::createImage($image->getName(), $thumbnailPath);
        return $thumbnail;
    }

    private function getThumbnailPathForImage(Image $image): string
    {
        $path = $image->getFullPath();
        $path = str_replace(Config::photoDir, Config::thumbnailDir, $path);
        return $path;
    }

    // @todo Add some checking that found thumbnail has the correct size, otherwise delete and redo perhaps
    // @todo Also what if the image has changed? Compare date of image and thumbnail, if image is newer redo
    private function thumbnailExists(Image $image): bool
    {
        return file_exists($this->getThumbnailPathForImage($image));
    }

    private function createThumbnail(FileManager $fileManager, Image $image): Image
    {
        // @todo do we need that?
        //ini_set('memory_limit', '96M');

        $pathToImage = $image->getFullPath();
        $width = $image->getWidth();
        $height = $image->getHeight();
        $type = $image->getType();

        $horizontalScale = Config::thumbnailMaxWidth * 1.0 / $width;
        $verticalScale = Config::thumbnailMaxHeight * 1.0 / $height;
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

        $thumbImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            $white = imagecolorallocate($thumbImage, 255, 255, 255);
            imagefill($thumbImage, 0, 0, $white);
        }

        if (Config::thumbnailResampleInsteadResize) {
            imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        } else {
            imagecopyresized($thumbImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        }

        $thumbnailPath = $this->getThumbnailPathForImage($image);
        $fileManager->createFoldersOfPath($fileManager->removeFileFromPath($thumbnailPath));
        imagejpeg($thumbImage, $thumbnailPath, Config::thumbnailJPEGQuality);

        imagedestroy($thumbImage);
        imagedestroy($srcImage);

        $thumbnail = new Image($image->getName(), $thumbnailPath, $type, $newWidth, $newHeight, time(), time(), new ExifData());
        return $thumbnail;
    }

    private function addThumbnailCreationRequestToBuffer(string $name, string $fullPath)
    {
        $this->thumbnailRequestBuffer[] = ["name" => $name, "path" => $fullPath];
    }

    // @todo also need information about the element of which the src has to be changed once the thumbnail is generated
    // @todo take care the generation events from nomrla pßage and slideshow don't interact -> js
    public function getThumbnailRequestHTML(): string
    {
        $html = "<div id='thumbnailCreationRequests'>";

        foreach($this->thumbnailRequestBuffer as $request) {
            $html .= "<div class='request'>";
            $html .= "<div class='name'>" . $request["name"] .= "</div>";
            $html .= "<div class='path'>" . $request["path"] .= "</div>";
            $html .= "</div>";
        }

        $html .= "</div>";
        return $html;
    }
}
