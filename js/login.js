$(function(){
    $(".menu-toggle").click(function(e){
        e.stopPropagation();
        $(".main-menu ul").toggleClass("show");
    });

    $(document).click(function(){
        $(".main-menu ul").removeClass("show");
    });

    $("#password_confirm").on("blur", function() {
        var pass = $("#password").val();
        var pass_confirm = $(this).val();

        if(pass !== pass_confirm) {
            $("#pass_msg").text("كلمة المرور وتأكيدها غير متطابقين!");
            $(this).css("border-color", "red");
        } else {
            $("#pass_msg").text("");
            $(this).css("border-color", "green");
        }
    });
})