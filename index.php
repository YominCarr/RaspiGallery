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

$fileManager = new FileManager();

echo "Photo dir (" . $fileManager->getAbsolutePhotoDir() . "):<br/>";
print_r($fileManager->scanDirRecursively($fileManager->getAbsolutePhotoDir()));
