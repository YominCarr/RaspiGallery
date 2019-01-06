var thumbnailRequests = [];
var currentRequests = [];
// @todo make max configurable
var maxRequests = 6; // Typically get 2 requests per thumbnail because of main page and slideshow

// @todo currently have dummy also in meta elements -> need to replace these too!

thumbnailEventLoop(); // Start the thumbnail creation event loop as soon as the page is done loading

// @todo Issue: thumbnails in slideshow which are not displayed from the beginning lack a request!

function thumbnailEventLoop() {
    extractRequestsFromDom();

    if (thumbnailRequests.length == 0) {
        setTimeout(thumbnailEventLoop, 5000); // Nothing to do, check again after 5 second
        return;
    }

    issueNewRequests();

    setTimeout(thumbnailEventLoop, 1000); // Start again after 1 second
}

function extractRequestsFromDom() {
    var hostContainer = document.getElementById("thumbnailCreationRequests");
    var requestContainers = hostContainer.getElementsByClassName("request");

    while (requestContainers.length > 0) {
        var requestContainer = requestContainers[0];

        var name = requestContainer.getElementsByClassName("name")[0].innerHTML;
        var path = requestContainer.getElementsByClassName("path")[0].innerHTML;
        var idSelector = requestContainer.getElementsByClassName("idSelector")[0].innerHTML;

        thumbnailRequests.push({"name": name, "path": path, "idSelector": idSelector});
        hostContainer.removeChild(requestContainer);
    }
}

function issueNewRequests() {
    for (var i = currentRequests.length; i < maxRequests && thumbnailRequests.length > 0; ++i) {
        var request = thumbnailRequests.shift();

        if (similarRequestInProgress(request)) {
            // This thumbnail is already in generation, requeue this one at the end; May happen due to slideshow
            // But should happen very rarely (only if the total number of thumbnails is very small)
            thumbnailRequests.push(request);
        } else {
            // Actually carry out that request
            postAjaxRequest("generateThumbnail.php", request, replaceImageSourcesAndRemoveRequest);
        }
    }
}

function similarRequestInProgress(request) {
    var similarRequestFound = false;
    for (var i = 0; i < currentRequests.length; ++i) {
        if (currentRequests[i].path == request.path) {
            similarRequestFound = true;
            break;
        }
    }
    return similarRequestFound;
}

function postAjaxRequest(url, data, successCallback) {
    var params = typeof data == 'string' ? data : Object.keys(data).map(
        function (k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
        }
    ).join('&');

    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', url);
    xhr.onreadystatechange = function () {
        if (xhr.readyState > 3 && xhr.status == 200) {
            successCallback(xhr.responseText);
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);

    return xhr;
}

function replaceImageSourcesAndRemoveRequest(json) {
    var data = JSON.parse(json);

    var idSelector = data.idSelector;
    var src = data.src;

    document.getElementById(idSelector).src = src;
    removeRequest(idSelector);
}

function removeRequest(idSelector) {
    currentRequests = currentRequests.filter(function (request) {
        return request.idSelector != idSelector;
    });
}
