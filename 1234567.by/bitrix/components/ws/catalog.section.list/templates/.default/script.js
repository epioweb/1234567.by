$(document).ready(function(){
    /*$(".list .relative span").hover(function(){
        $(this).parent().find("ul").show().addClass("activated");
    });*/

    /*$(".list .relative ul").hover(function(){
        if()
    });*/
    /*$(".list .menu-container>a").hover(function(){
        var nextObj = $(this).next();
        if(nextObj[0]) {
            if(nextObj[0].tagName == "UL") {
                var offset = $(this).offset();
                $(nextObj[0]).css({
                    left: (offset.left + 15) + "px",
                    top: offset.top + "px"
                });
                $(nextObj[0]).show();
            }
        }
    }, function(){
        var nextObj = $(this).next();
        if(nextObj[0]) {
            if(nextObj[0].tagName == "UL") {
                $(nextObj[0]).hide();
            }
        }
    });*/

    $(".list .menu-container>ul").hover(function(){
        $(this).show();
    }, function(){
        $(this).hide();
    });
});