<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/Gallery.css" />
</head>
<body>

<h1>Welcome to RaspiGallery</h1>

<?php
require_once __DIR__.'/src/Config.php';
require_once __DIR__.'/src/FileManager.php';
require_once __DIR__.'/src/ThumbnailManager.php';
require_once __DIR__.'/frontend/Gallery.php';

$fileManager = new FileManager();

$subDir = filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING);
$dir = $fileManager->getAbsolutePhotoDir();
if (! empty($subDir)) {
    $dir = $fileManager->concatPaths($dir, $subDir);
}
$content = $fileManager->scanDirRecursively($dir);

/*echo printContent($content);

echo "<br><br>";*/

$thumbnailManager = new ThumbnailManager();
$gallery = new Gallery($fileManager, $thumbnailManager);
echo $gallery->getFoldersAndImagesGalleryHTML($content);

?>

</body>
</html>
