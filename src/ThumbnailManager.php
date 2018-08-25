<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 16:43
 */

require_once 'FileManager.php';

class ThumbnailManager
{

    public function generateThumbnailIfNeeded(Image $image): bool
    {
        if (!$this->thumbnailExists($image)) {
            return $this->createThumbnail($image);
        }
        return true;
    }

    private function getThumbnailPathForImage(Image $image): string
    {
        $path = $image->getFullPath();
        $path = str_replace(Config::photoDir, Config::thumbnailDir, $path);
        return $path;
    }

    private function thumbnailExists(Image $image): bool
    {
        return file_exists($this->getThumbnailPathForImage($image));
    }

    private function createThumbnail(Image $image): bool
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
        // @todo rather throw exception?
        if (!$srcImage) return false;

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

        imagejpeg($thumbImage, $this->getThumbnailPathForImage($image), Config::thumbnailJPEGQuality);

        imagedestroy($thumbImage);
        imagedestroy($srcImage);
        return true;
    }
}