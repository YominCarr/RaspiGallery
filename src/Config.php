<?php

class SortingMethod {
    const fileName = 0;
    const fileCreationDate = 0;
    const fileModificationDate = 1;
    const photoShotDate = 2;
}

class SortingOrder {
    const ascending = 0;
    const descending = 1;
}

class Config
{
    const documentRoot = "RaspiGallery";
    const photoDir = "sampleData";
    const thumbnailDir = "thumbnails";

    const thumbnailMaxWidth = 300;
    const thumbnailMaxHeight = 200;

    const thumbnailResampleInsteadResize = true; // Better quality
    const thumbnailJPEGQuality = 75;

    const numberImagesPerRow = 4;

    const sortingKey = SortingMethod::fileName;
    const sortingOrder = SortingOrder::descending;

    // @todo actually use something like this
    // [['folder' => '', 'password' => ''], [...], ...]
    const protectedFolders = [];

}
