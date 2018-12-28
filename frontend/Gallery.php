<?php

require_once __DIR__ . '/../src/FileManager.php';
require_once __DIR__ . '/../src/ThumbnailManager.php';
require_once __DIR__.'/../src/Config.php';

// @todo Add links to images / folders
function getFoldersAndImagesGalleryHTML(FileManager $fileManager, ThumbnailManager $thumbnailManager, array $content): string
{
    $str = "<div class='galleryWrapper'>";

    $str .= "<h2>Folders</h2>";
    $str .= getFolderGalleryHTML($fileManager, $thumbnailManager, $content["folders"]);
    $str .= "<br/><br/>";
    $str .= "<h2>Images</h2>";
    $str .= getImageGalleryHTML($fileManager, $thumbnailManager, $content["images"]);

    $str .= "</div>";

    return $str;
}

function getFolderGalleryHTML(FileManager $fileManager, ThumbnailManager $thumbnailManager, array $folders) {
    $thumbnailHTMLs = [];

    foreach ($folders as $folder) {
        $image = $folder->getRandomImage($fileManager);
        // @todo else ?
        if ($image != NULL) {
            if (strpos($image->getFullPath(), "img/dummy.png") !== false) {
                $thumbnail = $image;
            } else {
                $thumbnail = $thumbnailManager->generateThumbnailIfNeeded($fileManager, $image);
            }
            $thumbnailHTMLs[] = $thumbnail->getDisplayHTML($fileManager);
        }
    }

    return getGalleryHTML($thumbnailHTMLs);
}

function getImageGalleryHTML(FileManager $fileManager, ThumbnailManager $thumbnailManager, array $images) {
    $thumbnailHTMLs = [];

    foreach ($images as $image) {
        $thumbnail = $thumbnailManager->generateThumbnailIfNeeded($fileManager, $image);
        $thumbnailHTMLs[] = $thumbnail->getDisplayHTML($fileManager); //<img src="img1.jpg" onclick="openModal();currentSlide(1)" class="hover-shadow">
    }

    return getGalleryHTML($thumbnailHTMLs);
}

function getGalleryHTML(array $thumbnailHTMLs) {
    $str = "<div class='gallery'><div class='thumbnailRow'>";

    $i = 0;
    foreach ($thumbnailHTMLs as $thumbnailHTML) {
        $str .= "<div class='thumbnailColumn'>";
        $str .= $thumbnailHTML;
        $str .= "</div>";

        ++ $i;
        if ($i % Config::numberImagesPerRow == 0) {
            $str .= "</div><div class='thumbnailRow'>";
        }
    }

    $str .= "</div></div>";
    return $str;
}

// @todo these will become obsolete soon
function printContent(array $content): string
{
    $ret = "<ul>";
    $ret .= printFolders($content["folders"]);
    $ret .= printImages($content["images"]);
    $ret .= "</ul>";
    return $ret;
}

function printFolders(array $folders): string
{
    $ret = "";
    foreach ($folders as $f) {
        $ret .= "<li>" . $f->getName() . "<ul>";
        $ret .= printContent($f->getContent());
        $ret .= "</ul></li>";
    }
    return $ret;
}

function printImages(array $images): string
{
    $ret = "";
    foreach ($images as $i) {
        $ret .= "<li>" . $i->getName() . "</li>";
    }
    return $ret;
}
