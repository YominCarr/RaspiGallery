function downloadImages() {
    var files = collectFilesForDownload();
    var names = getNamesFromFiles(files);
    var paths = getPathsFromFiles(files);

    var data = {
        "imageNames": JSON.stringify(names),
        "imagePaths": JSON.stringify(paths)
    };

    postAjaxRequest("downloadZip.php", data, outputToConsole);
}

function collectFilesForDownload() {
    return document.getElementsByClassName("downloadMe");
}

function getNamesFromFiles(files) {
    var names = [];

    for (var i = 0; i < files.length; ++i) {
        var file = files[i];
        names.push(file.alt);
    }

    return names;
}

function getPathsFromFiles(files) {
    var paths = [];

    for (var i = 0; i < files.length; ++i) {
        var file = files[i];
        paths.push(file.dataset.path)
    }

    return paths;
}
