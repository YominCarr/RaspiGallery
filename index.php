Hello RaspiGallery!<br/><br/>

<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 16:38
 */

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
