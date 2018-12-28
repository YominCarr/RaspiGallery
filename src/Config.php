<?php

class Config
{
    const documentRoot = "RaspiGallery";
    const photoDir = "sampleData";
    const thumbnailDir = "thumbnails";

    // Width must relate to settings in css/Gallery.css
    // @todo make that adjust automatically
    const thumbnailMaxWidth = 300;
    const thumbnailMaxHeight = 200;

    const thumbnailResampleInsteadResize = true; // Better quality
    const thumbnailJPEGQuality = 75;

    const numberImagesPerRow = 4;
}
