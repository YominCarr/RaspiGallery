<?php

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/FileManager.php';
require_once __DIR__ . '/src/ThumbnailManager.php';
require_once __DIR__ . '/src/Image.php';

$imgName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$imgPath = filter_input(INPUT_POST, 'path', FILTER_SANITIZE_STRING);
$imageIdSelector = filter_input(INPUT_POST, 'imageIdSelector', FILTER_SANITIZE_STRING);
$contentIdSelector = filter_input(INPUT_POST, 'contentIdSelector', FILTER_SANITIZE_STRING);

$fileManager = new FileManager();
$thumbnailManager = new ThumbnailManager();

$image = Image::createImage($imgName, $imgPath);

$thumbnail = $thumbnailManager->generateThumbnailIfNeeded($fileManager, $image);
$thumbnailUrl = $thumbnail->getRelativePathAsUrl($fileManager);

echo json_encode(["imageIdSelector" => $imageIdSelector, "contentIdSelector" => $contentIdSelector, "src" => $thumbnailUrl]);
