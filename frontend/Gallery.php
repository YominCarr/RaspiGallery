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
            $str .= $this->getSlideshowOverlayHTML($images);
        }

        $str .= $this->thumbnailManager->getThumbnailRequestHTML();

        $str .= "</div>";

        return $str;
    }

    private function getFolderGalleryHTML(array $folders)
    {
        $thumbnailHTMLs = [];

        for ($i = 0; $i < sizeof($folders); ++$i) {
            $folder = $folders[$i];

            if (Config::folderThumbnailDisplay == FolderThumbnail::first) {
                $image = $folder->getFirstImage($this->fileManager);
            } else if (Config::folderThumbnailDisplay == FolderThumbnail::random) {
                $image = $folder->getRandomImage($this->fileManager);
            }

            if ($image == NULL) {
                // @todo empty folder image! -> multiple dummy types
            } else {
                if (strpos($image->getFullPath(), "img/dummy.png") !== false) {
                    $thumbnail = $image;
                } else {
                    $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);
                }

                $id = "folderThumbnail$i";
                $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "hover-shadow", $id);

                if ($thumbnail->isDummy()) {
                    $this->thumbnailManager->addThumbnailCreationRequestToBuffer($image->getName(), $image->getFullPath(), $id);
                }

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

        for ($i = 0; $i < sizeof($images); ++$i) {
            $image = $images[$i];
            $id = "imageThumbnail$i";

            $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);
            $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "hover-shadow", $id);

            if ($thumbnail->isDummy()) {
                $this->thumbnailManager->addThumbnailCreationRequestToBuffer($image->getName(), $image->getFullPath(), $id);
            }

            $thumbnailHTMLs[] = $this->createSlideshowLinkAroundImage($thumbnailHTML, $i);
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

    private function getSlideshowOverlayHTML($images)
    {
        $str = "<div id=\"myModal\" class=\"modal\">";
        $str .= "<span class=\"close cursor\" onclick=\"closeModal()\">&times;</span>";

        $str .= $this->getSlideshowCaptionHTML($images);

        $str .= "<div class=\"modal-content\">";

        $str .= $this->getSlideshowFullImagesHTML($images);
        $str .= $this->getSlideshowControlsHTML();
        $str .= $this->getThumbnailsForSlideShowHTML($images);
        $str .= $this->getSlideshowImagesMetaHTML($images);

        $str .= "</div></div>";
        return $str;
    }

    private function getSlideshowFullImagesHTML(array $images): string
    {
        $str = "";
        $countImages = sizeof($images);

        for ($i = 0, $nr = 1; $i < Config::numberImagesPerRow && $i < $countImages; ++$i, ++$nr) {
            $image = $images[$i];

            $str .= "<div class='mySlides' id='mySlide$i'>";
            $str .= "<div class='numbertext' id='numbertext$i'>$nr / $countImages</div>";
            $str .= $image->getDisplayHTML($this->fileManager, "slideshowimage", "slideshowimage$i");
            $str .= "</div>";
        }
        return $str;
    }

    private function getSlideshowControlsHTML(): string
    {
        $str = "<a class=\"prev\" onclick=\"plusSlides(-1)\">&#10094;</a>";
        $str .= "<a class=\"next\" onclick=\"plusSlides(1)\">&#10095;</a>";
        return $str;
    }

    // @todo consider reading these with javascript once the image is loaded instead
    private function getSlideshowCaptionHTML(array $images): string
    {
        $str = "<div id=\"captionContainer\">";
        $str .= "<h2 id='exifHeading'>Exif Data</h2>";
        $str .= "<a id=\"openCloseExif\" onclick=\"closeExifBlock()\">&#10094;</a>";
        $str .= "<div id='exif'>";
        for ($i = 0; $i < Config::numberImagesPerRow && $i < sizeof($images); ++$i) {
            $image = $images[$i];

            $str .= "<p id=\"caption$i\" class='caption'>";
            $str .= $this->getSlideshowCaption($image);
            $str .= "</p>";

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

    private function getThumbnailsForSlideShowHTML(array $images): string
    {
        $str = "<div class='thumbnailRow'>";
        for ($i = 0; $i < Config::numberImagesPerRow && $i < sizeof($images); ++$i) {
            $image = $images[$i];
            $id = "demo$i";

            $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);
            $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "demo", $id);

            if ($thumbnail->isDummy()) {
                $this->thumbnailManager->addThumbnailCreationRequestToBuffer($image->getName(), $image->getFullPath(), $id);
            }

            $str .= "<div class=\"thumbnailColumn\" onclick='currentSlide($i)'>" . $thumbnailHTML . "</div>";
        }
        $str .= "</div>";
        return $str;
    }

    private function getSlideshowImagesMetaHTML(array $images): string
    {
        $str = "<div id=\"allImagesMeta\">";

        $countImages = sizeof($images);
        $str .= "<div id='countImages'>$countImages</div>";

        for ($i = 0; $i < $countImages; ++$i) {
            $image = $images[$i];
            $thumbnailSrcId = "image" . $i . "Meta_thumbSrc";

            $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);

            if ($thumbnail->isDummy()) {
                $this->thumbnailManager->addThumbnailCreationRequestToBuffer($image->getName(), $image->getFullPath(), "", $thumbnailSrcId);
            }

            $src = $image->getRelativePathAsUrl($this->fileManager);
            $alt = $image->getName();
            $caption = $this->getSlideshowCaption($image);
            $thumbSrc = $thumbnail->getRelativePathAsUrl($this->fileManager);

            $str .= "<div id='image" . $i . "Meta'>";
            $str .= "<div id='image" . $i . "Meta_src'>" . $src . "</div>";
            $str .= "<div id='image" . $i . "Meta_alt'>" . $alt . "</div>";
            $str .= "<div id='image" . $i . "Meta_caption'>" . $caption . "</div>";
            $str .= "<div id='$thumbnailSrcId'>" . $thumbSrc . "</div>";
            $str .= "</div>";
        }
        $str .= "</div>";

        return $str;
    }
}
