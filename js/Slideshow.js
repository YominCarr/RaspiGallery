// Open the Modal
function openModal() {
    document.getElementById('myModal').style.display = "block";
}

// Close the Modal
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
    var slides = document.getElementsByClassName("mySlides");
    var dots = document.getElementsByClassName("demo");
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
