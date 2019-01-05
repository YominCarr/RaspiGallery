<?php

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/FileManager.php';
require_once __DIR__ . '/src/ThumbnailManager.php';
require_once __DIR__ . '/src/Image.php';

$imgname = filter_input(INPUT_POST, 'imgname', FILTER_SANITIZE_STRING);
$imgpath = filter_input(INPUT_POST, 'imgpath', FILTER_SANITIZE_STRING);

$fileManager = new FileManager();
$thumbnailManager = new ThumbnailManager();

$image = Image::createImage($imgname, $impath);

$thumbnail = $thumbnailManager->generateThumbnailIfNeeded($fileManager, $image);
$thumbnailUrl = $thumbnail->getRelativePathAsUrl($fileManager);

echo json_encode(["src" => $thumbnailUrl]);
