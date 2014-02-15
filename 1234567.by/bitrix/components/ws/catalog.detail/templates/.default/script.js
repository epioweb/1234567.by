/**
 * Управление карточкой товара
 * */
function IsValidateEmail(email) {
      var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,6})$/;
      return reg.test(email);
}

 
L.ProductCard  = L.Class.extend({
    initialize: function() {
        this.basketUrl = "/personal/cart/";
        this.loading = false;
        this.events();
    },
    add2basket: function(rel, actor) {
        var arData = rel.split(":");
        actor.basketObj = new L.Basket(arData[0], arData[1], 1, this.add2basket_callback, actor);
        actor.basketObj.add();
        actor.loading = new L.LocalWaitWindow($(actor).parent());
    },
    add2basket_callback: function(data, caller){
        caller.loading.remove();
        $(caller).unbind("click");
        $(caller).on("click", function(){
            location.href = "/personal/cart/";
        }).find("span").text("Оформить");
    },
    showBigImage: function(obj) {
        var rel = $(obj).attr("rel");
        $("#" + rel).click();
        return false;
    },
    slideImage: function(obj) {
        var prevId = parseInt($(".thumbs li.active a").attr("rel").replace("photo-", ""), 10);
        $(".thumbs li").removeClass("active");
        $(obj).parent().addClass("active");
        var currentId = parseInt($(".thumbs li.active a").attr("rel").replace("photo-", ""), 10);

        var direction = "left";
        if(currentId < prevId) { direction = "right"; }

        //var imageHeight = $(".image img").eq(0).height();
        var animationValue = Math.abs(currentId - prevId) * 300;
        if (direction == "left") {
            animationValue = "-=" + animationValue + "px";
        } else {
            animationValue = "+=" + animationValue + "px";
        }

        $(".gallery .image ul").stop(true,true).animate({ "marginLeft": animationValue }, "fast");
    },
    events: function() {
        var parentObj = this;
        $('button.buy').on("click", function(){
            if($(this).hasClass("order"))
                location.href = parentObj.basketUrl;
            else
                parentObj.add2basket($(this).attr("rel"), this);
            return false;
        });

        $(".thumbs a").on("click", function(){
            parentObj.showBigImage($(this));
            return false;
        });

        $(".thumbs a").on("hover", function(){
            parentObj.slideImage($(this));
            return false;
        });
		$('button.subscribe').on("click", function(){
			$('.popup_shadow').animate({opacity: 'show'}, 400);
			$('#subscribe_form').animate({opacity: 'show'}, 400);	
		});
		$('.popup_shadow').on('click', function(){
			$('.popup_shadow').hide(0);
			$('#subscribe_form').hide(0);
		});
		$('.close-popup').on('click', function(){
			$('.popup_shadow').hide(0);
			$('#subscribe_form').hide(0);
		});
		$('#subscribe_btn').on('click', function(){
			var id=$('#product_id').val();
				console.log(id);
			var email=$('#email').val();
				console.log(email);
				if(IsValidateEmail(email)){
					$.get('/include/subscribe_ajax.php',{email: email, id: id});
					$('#product_id').remove();
					$('#email').remove();
					$('#subscribe_btn').remove();
					$('#error_email').remove();
					$('#subscribe_form').children('div').text("На E-mail "+email+" будет отправлено уведомление!");
					$('.subscribe').replaceWith("<div style='color:red; float: left;margin: 18px;'>Вы подписаны на этот товар</div>");
					setTimeout(function(){$('.popup_shadow').animate({opacity: 'hide'}, 1500);$('#subscribe_form').animate({opacity: 'hide'}, 3300);},1625);
				}
				else{
					alert('Вы ввели некоректный E-mail. Попробуйте еще раз.');
				}
			
		});
    }
	
});
$(document).ready(function(){
    L.ProductCardItem = new L.ProductCard();
});