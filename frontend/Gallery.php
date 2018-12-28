<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 19:18
 */

require_once __DIR__ . '/../src/FileManager.php';
require_once __DIR__ . '/../src/ThumbnailManager.php';

// @todo Need proper layout
// @todo Add links to images / folders
function getGalleryHTML(FileManager $fileManager, ThumbnailManager $thumbnailManager, array $content): string
{
    $str = "";

    foreach ($content["folders"] as $folder) {
        $image = $folder->getRandomImage($fileManager);
        if ($image != NULL) {
            if (strpos($image->getFullPath(), "img/dummy.png") !== false) {
                $thumbnail = $image;
            } else {
                $thumbnail = $thumbnailManager->generateThumbnailIfNeeded($fileManager, $image);
            }
            $str .= $thumbnail->getDisplayHTML($fileManager) . " (Folder)<br>";
        }
    }

    $str .= getImageRowHTML($fileManager, $thumbnailManager, $content["images"]);

    return $str;
}

function getImageRowHTML(FileManager $fileManager, ThumbnailManager $thumbnailManager, array $images) {
    $str = "<div class='thumbnailRow'>";

    foreach ($images as $image) {
        $str .= "<div class='thumbnailColumn'>";

        $thumbnail = $thumbnailManager->generateThumbnailIfNeeded($fileManager, $image);
        $str .= $thumbnail->getDisplayHTML($fileManager); //<img src="img1.jpg" onclick="openModal();currentSlide(1)" class="hover-shadow">

        $str .= "</div>";
    }

    $str .= "</div>";
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
