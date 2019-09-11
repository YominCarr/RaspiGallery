var imageIndex = 0;
var slideShowOpen = false;
var fullscreen = false;

function openModal() {
    document.getElementById('myModal').style.display = "block";
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
    unregisterKeyboardHandlers();
    slideShowOpen = false;
}

// Next/previous controls
function plusSlides(n) {
    imageIndex += n;
    showSlides();
}

// Thumbnail image controls
function currentSlide(n) {
    imageIndex = n;
    showSlides();
}

function showSlides() {
    if (!slideShowOpen) {
        registerKeyboardHandlers();
        slideShowOpen = true;
    }

    var container = document.getElementById("myModal");
    var slides = container.getElementsByClassName("mySlides");
    var captions = container.getElementsByClassName("caption");
    var thumbnails = container.getElementsByClassName("demo");

    var countImages = document.getElementById("countImages").innerHTML;

    if (imageIndex >= countImages) {
        imageIndex = 0
    }
    if (imageIndex < 0) {
        imageIndex = countImages - 1;
    }

    hideSlideshowElement(slides, captions, thumbnails);
    updateSlidesContent(slides, countImages);
    displayAppropriateSlide(slides, countImages, captions, thumbnails);
}

function registerKeyboardHandlers() {
    document.addEventListener('keydown', keyboardHandling);
}

function unregisterKeyboardHandlers() {
    document.removeEventListener('keydown', keyboardHandling);
}

function keyboardHandling(event) {
    switch (event.code) {
        case "ArrowLeft":
            plusSlides(-1);
            event.preventDefault();
            break;
        case "ArrowRight":
            plusSlides(1);
            event.preventDefault();
            break;
        case "ArrowDown":
            openExifBlock();
            event.preventDefault();
            break;
        case "ArrowUp":
            closeExifBlock();
            event.preventDefault();
            break;
        case "Escape":
            closeModal();
            event.preventDefault();
            break;
    }
}

function hideSlideshowElement(slides, captions, thumbnails) {
    for (var i = 0; i < slides.length; ++i) {
        slides[i].style.display = "none";
    }
    for (var i = 0; i < captions.length; ++i) {
        captions[i].style.display = "none";
    }
    for (var i = 0; i < thumbnails.length; ++i) {
        thumbnails[i].className = thumbnails[i].className.replace(" active", "");
    }
}

function updateSlidesContent(slides, countImages) {
    var firstImageIndex = calculateFirstImageIndexToDisplay(slides.length, countImages);
    for (var i = 0, j = firstImageIndex; i < slides.length && j < countImages; ++i, ++j) {
        displayImageInSlideContainer(j, i);
    }
}

function calculateFirstImageIndexToDisplay(displayCount, countImages) {
    var firstImageIndex = imageIndex - 1;

    if (imageIndex == 0) { // No image before
        firstImageIndex = 0;
    } else if (imageIndex >= countImages - (displayCount - 2)) { // Not enough images after
        firstImageIndex = countImages - displayCount;
    }

    if (firstImageIndex < 0) { //Forbid underflow
        firstImageIndex = 0;
    }

    return firstImageIndex;
}

function calculateSlideToDisplay(displayCount, countImages) {
    var slideIndex = 1;

    if (imageIndex == 0) { // No image before
        slideIndex = 0;
    } else if (imageIndex >= countImages - (displayCount - 2)) { // Not enough images after
        slideIndex = imageIndex - countImages + displayCount;
    }

    return slideIndex;
}

function displayImageInSlideContainer(imageId, containerId) {
    var metaIdBase = "image" + imageId + "Meta";

    var countImages = document.getElementById("countImages").innerHTML;
    var src = document.getElementById(metaIdBase + "_src").innerHTML;
    var alt = document.getElementById(metaIdBase + "_alt").innerHTML;
    var caption = document.getElementById(metaIdBase + "_caption").innerHTML;
    var thumbSrc = document.getElementById(metaIdBase + "_thumbSrc").innerHTML;

    var numbertextContainer = document.getElementById("numbertext" + containerId);
    var fullImageContainer = document.getElementById("slideshowimage" + containerId);
    var captionContainer = document.getElementById("caption" + containerId);
    var thumbnailContainer = document.getElementById("demo" + containerId);
    var thumbnailContainerParent = thumbnailContainer.parentElement;
    var fullImageLinkContainer = document.getElementById("fullImageLink" + containerId);

    changeNumberTextOverlay(numbertextContainer, imageId, countImages);
    changeImageSourceAndAlt(fullImageContainer, src, alt);
    changeElementContent(captionContainer, caption);
    changeImageSource(thumbnailContainer, thumbSrc);
    changeOnClickCurrentSlide(thumbnailContainerParent, imageId);
    changeLinkHref(fullImageLinkContainer, src);
}

function changeNumberTextOverlay(container, number, count) {
    changeElementContent(container, (number + 1) + " / " + count);
}

function changeElementContent(container, content) {
    container.innerHTML = content;
}

function changeImageSourceAndAlt(element, src, alt) {
    changeImageSource(element, src);
    element.alt = alt;
}

function changeImageSource(element, src) {
    element.src = src;
}

function changeOnClickCurrentSlide(element, number) {
    element.onclick = function () {
        currentSlide(number)
    };
}

function changeLinkHref(element, href) {
    element.href = href;
}

function displayAppropriateSlide(slides, countImages, captions, thumbnails) {
    var slideIndex = calculateSlideToDisplay(slides.length, countImages);

    slides[slideIndex].style.display = "block";
    captions[slideIndex].style.display = "block";
    thumbnails[slideIndex].className += " active";
}

function openExifBlock() {
    var controls = document.getElementById("openCloseExif");
    controls.style.transform = "rotate(90deg)";
    controls.onclick = closeExifBlock;

    var exif = document.getElementById("exif");
    exif.style.opacity = "1";
    exif.style.height = "unset";
}

function closeExifBlock() {
    var controls = document.getElementById("openCloseExif");
    controls.style.transform = "rotate(-90deg)";
    controls.onclick = openExifBlock;

    var exif = document.getElementById("exif");
    exif.style.opacity = "0";
    exif.style.height = "0px";
}

function toggleFullscreen() {
    var fullscreenIcon = document.getElementById("fullscreenIcon");
    var captionContainer = document.getElementById("captionContainer");
    var thumbnailRows = document.getElementsByClassName("thumbnailRow");
    var mainContent = document.getElementById("mainContent");
    var imageContainers = document.getElementsByClassName("slideshowimagecontainer");
    if (fullscreen) {
        fullscreenIcon.src = "img/maximize.svg";
        captionContainer.style.display = "inline-block";
        [].forEach.call(thumbnailRows, function (row) {
            row.style.display = "block";
        });
        mainContent.style.maxWidth = "1200px";
        [].forEach.call(imageContainers, function (img) {
            img.style.height = "74%";
        });
    } else {
        fullscreenIcon.src = "img/minimize.svg";
        captionContainer.style.display = "none";
        [].forEach.call(thumbnailRows, function (row) {
            row.style.display = "none";
        });
        mainContent.style.maxWidth = "unset";
        [].forEach.call(imageContainers, function (img) {
            img.style.height = "unset";
        });
    }
    fullscreen = !fullscreen;
}
