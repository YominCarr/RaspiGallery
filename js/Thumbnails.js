var thumbnailRequests = [];
var currentRequests = [];
// @todo make max configurable
var maxRequests = 3;

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

    for (var i = 0; i < requestContainers.length; ++i) {
        var requestContainer = requestContainers[i];

        var name = requestContainer.getElementsByClassName("name")[0].innerHTML;
        var path = requestContainer.getElementsByClassName("path")[0].innerHTML;
        var idSelector = requestContainer.getElementsByClassName("idSelector")[0].innerHTML;

        thumbnailRequests.push({"name": name, "path": path, "idSelector": idSelector});
        hostContainer.removeChild(requestContainer);
    }
}

function issueNewRequests() {
    for (var i = currentRequests.length; i < maxRequests && thumbnailRequests.length > 0; ++i) {
        // @todo only if not a request with the same image is currently processing
        var request = thumbnailRequests.shift();
        currentRequests.push(request);

        postAjaxRequest("generateThumbnail.php", request, replaceImageSourcesAndRemoveRequest);
    }
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
