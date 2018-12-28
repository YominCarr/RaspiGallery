<?php

require_once __DIR__.'/../src/Config.php';

header("Content-type: text/css; charset: UTF-8");

?>

.thumbnailColumn .emptyDummyImage {
    width: <?php echo Config::thumbnailMaxWidth ?>px;
    height: <?php echo Config::thumbnailMaxHeight ?>px;
}

<?php
// 0.9 to account for margins / paddings as an estimation
$widthPerPic = (100.0 / Config::numberImagesPerRow) * 0.9;
?>

.thumbnailColumn {
    min-width: <?php echo $widthPerPic; ?>%;
    max-width: <?php echo $widthPerPic; ?>%;
}
