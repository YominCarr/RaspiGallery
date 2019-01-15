function downloadImages() {
    var files = collectFilesForDownload();
    var names = getNamesFromFiles(files);
    var paths = getPathsFromFiles(files);

    var data = {
        "imageNames": JSON.stringify(names),
        "imagePaths": JSON.stringify(paths)
    };

    postAjaxRequest("downloadZip.php", data, handleDownloadResponse);
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

function handleDownloadResponse(data, json) {
    try {
        var data = JSON.parse(json);

        var downloadName = data.name;
        var downloadPath = data.path;
        navigateToDownload(downloadName, downloadPath);
    } catch (e) {
        console.log(e);
        console.log(json);

        alert("Error occured - Download failed");
    }
}

function navigateToDownload(name, path) {
    var downloadLink = document.createElement('a');
    downloadLink.href = path;
    downloadLink.download = name;
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
