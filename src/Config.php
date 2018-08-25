<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 16:49
 */

class Config
{
    const documentRoot = "RaspiGallery";
    const photoDir = "sampleData/";
    const thumbnailDir = "thumbnails/";

    const thumbnailMaxWidth = 300;
    const thumbnailMaxHeight = 200;

    const thumbnailResampleInsteadResize = true; // Better quality
    const thumbnailJPEGQuality = 75;
}
