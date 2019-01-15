<?php

require_once __DIR__ . '/src/ZipFileManager.php';

$imageNames = filter_input(INPUT_POST, 'imageNames');
$imagePaths = filter_input(INPUT_POST, 'imagePaths');

$imageNames = json_decode($imageNames);
$imagePaths = json_decode($imagePaths);

// @todo destination configurable
$name = 'photos.zip';
$destination = 'photos.zip';

$zipFileManager = new ZipFileManager();
$zipFileManager->createZip($imageNames, $imagePaths, $destination);

echo json_encode(["name" => $name, "path" => $destination]);


// @todo also need mechanism to remove old files
/*if (file_exists('photos.zip')) {
    unlink('photos.zip');
}*/
