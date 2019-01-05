function openModal() {
    document.getElementById('myModal').style.display = "block";
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

var lastSlideIndex = 1, slideIndex = 1;

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
    
    if (slideIndex > slides.length) {
        slideIndex = 1
    }
    if (slideIndex < 1) {
        slideIndex = slides.length
    }
    
    var caption = document.getElementById("caption" + slideIndex);
    
    for (var i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    for (var i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex - 1].style.display = "block";
    dots[slideIndex - 1].className += " active";
    lastCaption.style.display = "none";
    caption.style.display = "block";
}

function changeImageSource(element, src) {
    element.src = src;
}
