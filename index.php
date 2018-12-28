<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/Gallery.css" />
</head>
<body>

<?php

require_once __DIR__.'/src/Config.php';
require_once __DIR__.'/src/FileManager.php';
require_once __DIR__.'/src/ThumbnailManager.php';
require_once __DIR__.'/frontend/Gallery.php';

$fileManager = new FileManager();

$content = $fileManager->scanDirRecursively($fileManager->getAbsolutePhotoDir());

echo printContent($content);

echo "<br><br>";

$thumbnailManager = new ThumbnailManager();
echo getGalleryHTML($fileManager, $thumbnailManager, $content);

?>

</body>
</html>
