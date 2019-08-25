<?php

class FolderThumbnail {
    const first = 0;
    const random = 1;
}

class SortingMethod {
    const fileName = 0;
    const fileCreationDate = 1;
    const fileModificationDate = 2;
    const photoShotDate = 3;
}

class SortingOrder {
    const ascending = 0;
    const descending = 1;
}

class Config
{
    const documentRoot = "RaspiGallery";
//    const photoDir = "sampleData";
    const photoDir = "fotos-ssd";
    const thumbnailDir = "thumbnails";

    const thumbnailMaxWidth = 300;
    const thumbnailMaxHeight = 200;

    // Set to true for better quality
    const thumbnailResampleInsteadResize = true;
    const thumbnailJPEGQuality = 75;

    const folderThumbnailDisplay = FolderThumbnail::random;

    const numberImagesPerRow = 4;

    const imageSortingKey = SortingMethod::photoShotDate;
    const imageSortingOrder = SortingOrder::descending;

    // Folders always sort by name
    const folderSortingOrder = SortingOrder::descending;

    // @todo actually use something like this
    // [['folder' => '', 'password' => ''], [...], ...]
    const protectedFolders = [];

}
