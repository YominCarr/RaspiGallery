<?php

require_once __DIR__ . '/src/IncludeHeader.php';

require_once __DIR__.'/src/FileManager.php';

$fileManager = new FileManager();

$subDir = filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING);
$dir = $fileManager->getAbsolutePhotoDir();
if (! empty($subDir)) {
    $dir = $fileManager->concatPaths($dir, $subDir);
}
$folders = $fileManager->getShallowSubfolderList($dir);

$folderNames = array();
foreach ($folders as $folder) {
    array_push($folderNames, $folder->getRelativePath($fileManager));
}

echo encapsulateDataAndResponseInJson(RESPONSE_NO_ERROR, '', $folderNames);
