<?php

require_once __DIR__.'/../src/Config.php';

header("Content-type: text/css; charset: UTF-8");

?>

.thumbnailColumn .emptyDummyImage {
    width: <?php echo Config::thumbnailMaxWidth ?>px;
    height: <?php echo Config::thumbnailMaxHeight ?>px;
}

.thumbnailColumn {
    width: <?php echo Config::thumbnailMaxWidth; ?>px;
    height: <?php echo Config::thumbnailMaxHeight; ?>px;
}
