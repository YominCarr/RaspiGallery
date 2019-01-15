<?php

require_once __DIR__ . '/src/ZipFileManager.php';

$imageNames = filter_input(INPUT_POST, 'imageNames', FILTER_SANITIZE_STRING);
$imagePaths = filter_input(INPUT_POST, 'imagePaths', FILTER_SANITIZE_STRING);

// @todo destination configurable
$zipFileManager = new ZipFileManager();
$zipFileManager->createZip($imageNames, $imagePaths, 'photos.zip');

if (file_exists('photos.zip')) {
    //Set Headers:
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime('photos.zip')) . ' GMT');
    header('Content-Type: application/force-download');
    header('Content-Disposition: inline; filename="photos.zip"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize('photos.zip'));
    header('Connection: close');
    readfile('photos.zip');
    exit();
}

// @todo also need mechanism to remove old files
if (file_exists('photos.zip')) {
    unlink('photos.zip');
}
