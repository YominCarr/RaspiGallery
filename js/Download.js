function downloadImages() {
    $files = collectFilesForDownload();
    console.log("Download:");
    console.log($files);
}

function collectFilesForDownload() {
    return document.getElementsByClassName("downloadMe");
}