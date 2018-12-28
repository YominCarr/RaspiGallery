<?php

class Config
{
    const documentRoot = "RaspiGallery";
    const photoDir = "sampleData";
    const thumbnailDir = "thumbnails";

    // Width must match that of .thumbnailColumn in css/Gallery.css
    const thumbnailMaxWidth = 300;
    const thumbnailMaxHeight = 200;

    const thumbnailResampleInsteadResize = true; // Better quality
    const thumbnailJPEGQuality = 75;

    const numberImagesPerRow = 4;
}
