$(function(){
    $(".menu-toggle").click(function(e){
        e.stopPropagation();
        $(".main-menu ul").toggleClass("show");
    });

    $(document).click(function(){
        $(".main-menu ul").removeClass("show");
    });
})