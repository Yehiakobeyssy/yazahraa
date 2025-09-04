$(function(){
    var $wrapper = $(".news-wrapper");
    var wrapperWidth = $wrapper.width(); // طول المحتوى
    var containerWidth = $wrapper.parent().width();
    
    // السرعة: pixels per second
    var speed = 100; // 100px/sec تقريبًا شريط التلفاز
    
    // مدة الحركة
    var duration = (wrapperWidth + containerWidth) / speed; // بالثواني

    $wrapper.css({
        "animation": "ticker " + duration + "s linear infinite"
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