<?php

require_once __DIR__ . '/src/IncludeHeader.php';

require_once __DIR__.'/src/FileManager.php';

$fileManager = new FileManager();

$subDir = filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING);
$dir = $fileManager->getAbsolutePhotoDir();
if (! empty($subDir)) {
    $dir = $fileManager->concatPaths($dir, $subDir);
}
$images = $fileManager->getImagesList($dir);

$imageNames = array();
foreach ($images as $image) {
    array_push($imageNames, $image->getRelativePathToPhotoDir($fileManager));
}

echo encapsulateDataAndResponseInJson(RESPONSE_NO_ERROR, '', $imageNames);
