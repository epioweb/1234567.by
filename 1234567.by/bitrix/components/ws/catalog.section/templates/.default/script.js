/**
 * Управление списком товаров
 * */
L.ProductList  = L.Class.extend({
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
    events: function() {
        var parentObj = this;
        $('button.buy').on("click", function(){
            if($(this).hasClass("order"))
                location.href = parentObj.basketUrl;
            else
                parentObj.add2basket($(this).attr("rel"), this);
            return false;
        });
    }
});

$(document).ready(function(){
    L.ProductListItem = new L.ProductList();
});