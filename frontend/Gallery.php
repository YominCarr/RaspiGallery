<?php

require_once __DIR__ . '/../src/FileManager.php';
require_once __DIR__ . '/../src/ThumbnailManager.php';
require_once __DIR__ . '/../src/Config.php';

class Gallery
{

    private $fileManager;
    private $thumbnailManager;

    public function __construct(FileManager $fileManager, ThumbnailManager $thumbnailManager)
    {
        $this->fileManager = $fileManager;
        $this->thumbnailManager = $thumbnailManager;
    }

// @todo Add links to images / folders
    public function getFoldersAndImagesGalleryHTML(array $content): string
    {
        $str = "<div class='galleryWrapper'>";

        $folders = $content["folders"];
        $images = $content["images"];

        if (sizeof($folders) > 0) {
            $str .= "<h2>Folders</h2>";
            $str .= $this->getFolderGalleryHTML($folders);
        }
        if (sizeof($folders) > 0 && sizeof($images) > 0) {
            $str .= "<br/><br/>";
        }
        if (sizeof($images) > 0) {
            $str .= "<h2>Images</h2>";
            $str .= $this->getImageGalleryHTML($images);

            $str .= "<script type='text/javascript' src='js/Slideshow.js'></script>";
            $str .= $this->getSlideshowOverlay($images);
        }

        $str .= "</div>";

        return $str;
    }

    private function getFolderGalleryHTML(array $folders)
    {
        $thumbnailHTMLs = [];

        foreach ($folders as $folder) {
            $image = $folder->getRandomImage($this->fileManager);
            // @todo else ?
            if ($image != NULL) {
                if (strpos($image->getFullPath(), "img/dummy.png") !== false) {
                    $thumbnail = $image;
                } else {
                    $thumbnail = $this->thumbnailManager->generateThumbnailIfNeeded($this->fileManager, $image);
                }

                $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "hover-shadow");
                $folderPath = $this->fileManager->trimTrailingDirSeparator($folder->getRelativePathToPhotoDir($this->fileManager));
                $thumbnailHTMLs[] = $this->createFolderLinkAroundImage($folderPath, $thumbnailHTML);
            }
        }

        return $this->getGalleryHTML($thumbnailHTMLs);
    }

    private function createFolderLinkAroundImage($imagePath, $imageHTML)
    {
        return "<a href='" . $_SERVER['REQUEST_URI'] . "?path=$imagePath'>" . $imageHTML . "</a>";
    }

    private function getImageGalleryHTML(array $images)
    {
        $thumbnailHTMLs = [];

        $i = 1;
        foreach ($images as $image) {
            $thumbnail = $this->thumbnailManager->generateThumbnailIfNeeded($this->fileManager, $image);
            $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "hover-shadow");
            $thumbnailHTMLs[] = $this->createSlideshowLinkAroundImage($thumbnailHTML, $i);
            ++ $i;
        }

        return $this->getGalleryHTML($thumbnailHTMLs);
    }

    private function createSlideshowLinkAroundImage($imageHTML, $i)
    {
        return "<a onclick='openModal();currentSlide($i)'>" . $imageHTML . "</a>";
    }

    private function getGalleryHTML(array $thumbnailHTMLs)
    {
        $str = "<div class='gallery'><div class='thumbnailRow'>";

        $i = 0;
        foreach ($thumbnailHTMLs as $thumbnailHTML) {
            $str .= "<div class='thumbnailColumn'>";
            $str .= $thumbnailHTML;
            $str .= "</div>";

            ++$i;
            if ($i % Config::numberImagesPerRow == 0) {
                $str .= "</div><div class='thumbnailRow'>";
            }
        }

        while ($i % Config::numberImagesPerRow != 0) {
            $str .= "<div class='thumbnailColumn'>";
            $str .= $this->getEmptyThumbnailHTML();
            $str .= "</div>";

            ++$i;
        }

        $str .= "</div></div>";
        return $str;
    }

    private function getEmptyThumbnailHTML()
    {
        return "<div class='emptyDummyImage'></div>";
    }

    // @todo refactor
    // @todo only show a few images!
    private function getSlideshowOverlay($images)
    {
        $countImages = sizeof($images);

        $str = "<div id=\"myModal\" class=\"modal\">";
        $str .= "<span class=\"close cursor\" onclick=\"closeModal()\">&times;</span>";
        $str .= "<div class=\"modal-content\">";

        // Full sized images
        $i = 1;
        foreach ($images as $image) {

            $str .= "<div class=\"mySlides\">";
            $str .= "<div class=\"numbertext\">$i / $countImages</div>";
            $str .= $image->getDisplayHTML($this->fileManager, "slideshowimage");
            $str .= "</div>";

            ++$i;
        }

        // Controls
        $str .= "<a class=\"prev\" onclick=\"plusSlides(-1)\">&#10094;</a>";
        $str .= "<a class=\"next\" onclick=\"plusSlides(1)\">&#10095;</a>";

        // Caption
        $str .= "<div class=\"caption-container\">";
        $i = 1;
        foreach ($images as $image) {
            $str .= "<p id=\"caption$i\" class='caption'>";
            $str .= $this->getSlideshowCaption($image);
            $str .= "</p>";

            ++$i;
        }
        $str .= "</div>";

        // Thumbnails
        $i = 1;
        foreach ($images as $image) {
            $thumbnail = $this->thumbnailManager->generateThumbnailIfNeeded($this->fileManager, $image);
            $str .= "<div class=\"thumbnailColumn\" onclick='currentSlide($i)'>";
            $str .= $thumbnail->getDisplayHTML($this->fileManager, "demo");
            $str .= "</div>";

            ++$i;
        }

        $str .= "</div></div>";
        return $str;
    }

    private function getSlideshowCaption(Image $image): string
    {
        $exifData = $image->getExifData();
        $exifDataArray = ["Name" => $image->getName(),
            "Camera Modell" => $exifData->camera,
            "Sensitivity" => $exifData->iso,
            "Shutter Speed" => $exifData->shutter,
            "Aperture" => $exifData->aperture,
            "Focal Length" => $exifData->focal,
            "Date Taken" => $exifData->dateTaken];

        $exifString = "<table class='captionTable'>";
        foreach ($exifDataArray as $key => $val) {
            $exifString .= "<tr><td>" . $key . "</td><td>" . $val . "</td></tr>";
        }
        $exifString .= "</table>";

        return $exifString;
    }
}
