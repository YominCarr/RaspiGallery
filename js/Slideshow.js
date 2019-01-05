function openModal() {
    document.getElementById('myModal').style.display = "block";
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

var lastSlideIndex = 0, slideIndex = 0;

// Next/previous controls
function plusSlides(n) {
    lastSlideIndex = slideIndex;
    slideIndex += n;
    showSlides();
}

// Thumbnail image controls
function currentSlide(n) {
    lastSlideIndex = slideIndex;
    slideIndex = n;
    showSlides();
}

function showSlides() {
    var container = document.getElementById("myModal");
    var slides = container.getElementsByClassName("mySlides");
    var dots = container.getElementsByClassName("demo");
    var lastCaption = document.getElementById("caption" + lastSlideIndex);

    if (slideIndex >= slides.length) {
        slideIndex = 0
    }
    if (slideIndex < 0) {
        slideIndex = slides.length
    }

    var caption = document.getElementById("caption" + slideIndex);

    for (var i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    for (var i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex].style.display = "block";
    dots[slideIndex].className += " active";
    lastCaption.style.display = "none";
    caption.style.display = "block";
}

function displayImageInSlideContainer(imageId, containerId) {
    var metaIdBase = "image" + imageId;

    var countImages = document.getElementById("countImages").innerHTML;
    var src = document.getElementById(metaIdBase + "_src").innerHTML;
    var alt = document.getElementById(metaIdBase + "_alt").innerHTML;
    var caption = document.getElementById(metaIdBase + "_caption").innerHTML;
    var thumbSrc = document.getElementById(metaIdBase + "_thumbSrc").innerHTML;

    var numbertextContainer = document.getElementById("numbertext" + containerId);
    var fullImageContainer = document.getElementById("slideshowimage" + containerId);
    var captionContainer = document.getElementById("caption" + containerId);
    var thumbnailContainer = document.getElementById("demo" + containerId);

    changeNumberTextOverlay(numbertextContainer, imageId, countImages);
    changeImageSourceAndAlt(fullImageContainer, src, alt);
    changeElementContent(captionContainer, caption);
    changeImageSource(thumbnailContainer, thumbSrc);
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
