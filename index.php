<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/Gallery.css" />
    <link rel='stylesheet' type='text/css' href='css/GalleryComputed.php' />
    <link rel="stylesheet" type="text/css" href="css/Slideshow.css" />
    <link rel="stylesheet" type="text/css" href="css/Buttons.css" />
    <script type='text/javascript' src='js/Download.js'></script>
</head>
<body>

<h1>Welcome to RaspiGallery</h1>

<?php
require_once __DIR__.'/src/Config.php';
require_once __DIR__.'/src/FileManager.php';
require_once __DIR__.'/src/ThumbnailManager.php';
require_once __DIR__.'/frontend/Gallery.php';

echo "<br/><br/><h3><a href='" . $_SERVER['PHP_SELF'] . "'>Home</a></h3>";

$fileManager = new FileManager();

$subDir = filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING);
$dir = $fileManager->getAbsolutePhotoDir();
if (! empty($subDir)) {
    $dir = $fileManager->concatPaths($dir, $subDir);
}
$content = $fileManager->scanDirRecursively($dir);

$thumbnailManager = new ThumbnailManager();
$gallery = new Gallery($fileManager, $thumbnailManager);
echo $gallery->getFoldersAndImagesGalleryHTML($content);

?>

<?php // Must be at the end as it starts an event loop which requires a fully loaded dom ?>
<script type='text/javascript' src='js/Thumbnails.js'></script>
</body>
</html>
