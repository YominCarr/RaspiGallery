Hello RaspiGallery!<br/><br/>

<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 16:38
 */

require_once 'src/Config.php';
require_once 'src/FileManager.php';
require_once 'src/ThumbnailManager.php';
require_once 'frontend/Gallery.php';

$fileManager = new FileManager();

$content = $fileManager->scanDirRecursively($fileManager->getAbsolutePhotoDir());

echo printContent($content);

$thumbnailManager = new ThumbnailManager();
foreach ($content["images"] as $i) {
    echo ($thumbnailManager->generateThumbnailIfNeeded($i) ? "Thumbnails created" : "Error encountered in thumbnail generation");
}
