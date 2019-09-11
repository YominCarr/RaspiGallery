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
            $str .= "<input type='button' value='Download' onclick='downloadImages()' id='downloadButton' class='button blue' /><br/>";
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

            if ($folder->hasImages()) {
                if (Config::folderThumbnailDisplay == FolderThumbnail::first) {
                    $image = $folder->getFirstImage($this->fileManager);
                } else if (Config::folderThumbnailDisplay == FolderThumbnail::random) {
                    $image = $folder->getRandomImage($this->fileManager);
                }
            } else {
                if (Config::scanMethod == ScanMethod::topLevel) {
                    $image = Image::getTextboxFakeImage($folder->getName());
                } else {
                    $image = Image::getNoImagesDummyImage($this->fileManager);
                }
            }

            if (!$image->isValidImage() || strpos($image->getFullPath(), "img/dummy.png") !== false) { // TODO I think this can be simplified
                $thumbnail = $image;
            } else {
                $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);
            }

            $id = "folderThumbnail$i";
            $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "hover-shadow", $id);

            if ($thumbnail->isThumbnaiLCreationDummy()) {
                $this->thumbnailManager->addThumbnailCreationRequestToBuffer($image->getName(), $image->getFullPath(), $id);
            }

            $folderPath = $this->fileManager->trimTrailingDirSeparator($folder->getRelativePathToPhotoDir($this->fileManager));
            $thumbnailHTMLs[] = $this->createFolderLinkAroundImage($folderPath, $thumbnailHTML);
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
            $dataAttributes = ["data-path" => $image->getFullPath()];

            $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);
            // @todo make images for download selectable and then give them the class downloadMe - not all
            $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "hover-shadow downloadable downloadMe", $id, "", $dataAttributes);

            if ($thumbnail->isThumbnaiLCreationDummy()) {
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

        $str .= "<div class=\"modal-content\"><div id='mainContent'>";

        $str .= $this->getSlideshowFullImagesHTML($images);
        $str .= $this->getSlideshowControlsHTML();

        $str .= "</div>";
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
            $imageHTML = $image->getDisplayHTML($this->fileManager, "slideshowimage", "slideshowimage$i");;

            $str .= "<div class='mySlides' id='mySlide$i'>";
            $str .= "<div class='numbertext' id='numbertext$i'>$nr / $countImages</div>";
            $str .= "<div class='slideshowimagecontainer'>" . $this->createFullImageLinkAroundImage($image, $imageHTML, $i) . "</div>";
            $str .= "</div>";
        }
        return $str;
    }

    private function createFullImageLinkAroundImage($image, $imageHTML, $i)
    {
        $link = $image->getRelativePathAsUrl($this->fileManager);
        $id = "fullImageLink$i";

        return "<a href='$link' id='$id' target='_blank'>" . $imageHTML . "</a>";
    }

    private function getSlideshowControlsHTML(): string
    {
        $str = "<a class=\"prev\" onclick=\"plusSlides(-1)\">&#10094;</a>";
        $str .= "<a class=\"next\" onclick=\"plusSlides(1)\">&#10095;</a>";
        $str .= "<a class=\"fullscreen\" onclick=\"toggleFullscreen()\"><img id='fullscreenIcon' src='img/maximize.svg' /></a>";
        return $str;
    }

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
            "Image Size" => $image->getWidth() . " x " . $image->getHeight() . " Pixels",
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
        $str = "<div class='thumbnailRow slideshowThumbnailRow'>";
        for ($i = 0; $i < Config::numberImagesPerRow && $i < sizeof($images); ++$i) {
            $image = $images[$i];
            $id = "demo$i";

            $thumbnail = $this->thumbnailManager->getThumbnailOrDummy($this->fileManager, $image);
            $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager, "demo", $id);

            if ($thumbnail->isThumbnaiLCreationDummy()) {
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

            if ($thumbnail->isThumbnaiLCreationDummy()) {
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
