$(function(){
var $wrapper = $(".news-wrapper");
var wrapperWidth = $wrapper.width();   // طول المحتوى
var containerWidth = $wrapper.parent().width();

// السرعة ثابتة px/sec
var speed = 50;

// نضيف containerWidth كـ padding للبداية والنهاية
var duration = (wrapperWidth + containerWidth * 2) / speed;

$wrapper.css({
  "animation-duration": duration + "s"
});


     $(".menu-toggle").click(function(e){
        e.stopPropagation();
        $(".main-menu ul").toggleClass("show");
    });

    $(document).click(function(){
        $(".main-menu ul").removeClass("show");
    });

    $(".news-wrapper").addClass("animate");

    // slide show 
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let slides = document.getElementsByClassName("mySlide");
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1;}
        slides[slideIndex-1].style.display = "block";
        setTimeout(showSlides, 5000); // 5 ثواني لكل صورة
    }
});