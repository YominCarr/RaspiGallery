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

                $thumbnailHTML = $thumbnail->getDisplayHTML($this->fileManager);
                $thumbnailPath = $this->fileManager->trimTrailingDirSeparator($folder->getRelativePathToPhotoDir($this->fileManager));
                $thumbnailHTMLs[] = $this->createFolderLinkAroundImage($thumbnailPath, $thumbnailHTML);
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

        foreach ($images as $image) {
            $thumbnail = $this->thumbnailManager->generateThumbnailIfNeeded($this->fileManager, $image);
            $thumbnailHTMLs[] = $thumbnail->getDisplayHTML($this->fileManager); //<img src="img1.jpg" onclick="openModal();currentSlide(1)" class="hover-shadow">
        }

        return $this->getGalleryHTML($thumbnailHTMLs);
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
        // @todo use these to inject css
        $width = CONFIG::thumbnailMaxWidth;
        $height = CONFIG::thumbnailMaxHeight;
        return "<div class='emptyDummyImage'></div>";
    }
}
