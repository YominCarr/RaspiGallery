var thumbnailRequests = [];
var currentRequests = [];
// @todo make max configurable
var maxRequests = 4; // Typically get 2-3 requests per thumbnail because of main page, slideshow and meta elements

thumbnailEventLoop(); // Start the thumbnail creation event loop as soon as the page is done loading

// @todo Issue: What if two people want to generate the same thumbnail at the same time?
// @todo What about server load - in case many people visit the page a lot of requests are issued at the same time
function thumbnailEventLoop() {
    extractRequestsFromDom();

    if (thumbnailRequests.length == 0) {
        setTimeout(thumbnailEventLoop, 5000); // Nothing to do, check again after 5 seconds
        return;
    }

    issueNewRequests();

    setTimeout(thumbnailEventLoop, 3000); // Start again after 3 seconds; Less time does not allow proper browser updates
}

function extractRequestsFromDom() {
    var hostContainer = document.getElementById("thumbnailCreationRequests");
    var requestContainers = hostContainer.getElementsByClassName("request");

    while (requestContainers.length > 0) {
        var requestContainer = requestContainers[0];

        var name = requestContainer.getElementsByClassName("name")[0].innerHTML;
        var path = requestContainer.getElementsByClassName("path")[0].innerHTML;

        var imageIdSelector = "";
        var imageIdSelectorContainers = requestContainer.getElementsByClassName("imageIdSelector");
        if (imageIdSelectorContainers.length > 0) {
            imageIdSelector = imageIdSelectorContainers[0].innerHTML;
        }

        var contentIdSelector = "";
        var contentIdSelectorContainers = requestContainer.getElementsByClassName("contentIdSelector");
        if (contentIdSelectorContainers.length > 0) {
            contentIdSelector = contentIdSelectorContainers[0].innerHTML;
        }

        thumbnailRequests.push({
            "name": name,
            "path": path,
            "imageIdSelector": imageIdSelector,
            "contentIdSelector": contentIdSelector
        });
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

    var imageIdSelector = data.imageIdSelector;
    var contentIdSelector = data.contentIdSelector;
    var src = data.src;

    if (imageIdSelector != "") {
        document.getElementById(imageIdSelector).src = src;
        removeRequestUsingImageIdSelector(imageIdSelector);
    }
    if (contentIdSelector != "") {
        document.getElementById(contentIdSelector).innerHTML = src;
        removeRequestUsingContentIdSelector(contentIdSelector);
    }
}

function removeRequestUsingImageIdSelector(imageIdSelector) {
    currentRequests = currentRequests.filter(function (request) {
        return request.imageIdSelector != imageIdSelector;
    });
}

function removeRequestUsingContentIdSelector(contentIdSelector) {
    currentRequests = currentRequests.filter(function (request) {
        return request.contentIdSelector != contentIdSelector;
    });
}
