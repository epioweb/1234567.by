$(document).ready(function(){
    $("a.more").on("click", function(){
        var text = "Свернуть";
        if($(this).hasClass("close")) {
            text = "Далее";
            $(this).prev().slideUp();
        } else {

            $(this).prev().slideDown();
        }
        $(this).text(text);
        $(this).toggleClass("close");
        return false
    });
});