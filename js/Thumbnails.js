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
            currentRequests.push(request);
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

function replaceImageSourcesAndRemoveRequest(request, json) {
    try {
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
    } catch (e) {
        console.log(e);
        console.log(json);

        postAjaxRequest("generateThumbnail.php", request, replaceImageSourcesAndRemoveRequest);
    }

    if (currentRequests.length == 0) {
        setTimeout(thumbnailEventLoop, 1000); // Start again after 1 second; Less time does n0ot give the browser enough update time
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
