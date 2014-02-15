L.BasketPage = L.Class.extend({
    initialize: function() {
        this.delButton = $(".basket_page .delete"); // кнопки удаления товара
        this.quantityInput = $(".num input");// количество товара в корзине
        this.minusButton = $(".basket_page .mn");   // минус 1
        this.plusButton = $(".basket_page .pl");    // плюс 1
        this.basketTable = $(".basket_page div.table>table"); // таблица корзины


        this._url = "/tools/add_to_basket.php";     // обработчик корзины
        this.events();
    },
    getProductID: function(obj) {
        this.parentRow = $(obj).closest("tr");
        if(this.parentRow) {
            this.quantityInput = this.parentRow.find(".num input");
            this.productID = this.parentRow.attr("rel");
            if(this.productID)
                this.productID = parseInt(this.productID, 10);
            else
                this.productID = 0;

            return this.productID;
        }
        else
            return 0;
    },
    getQuantity: function() {
        return parseInt(this.quantityInput.val(), 10);
    },
    update: function(obj, mode) {
        var params = {}, productQuantity;

        params.id = this.getProductID(obj);
        productQuantity = this.getQuantity();

        if(!productQuantity) {
            return false;
        }

        params.ACTION = "ADD2BASKET";

        // рассчитать количество, установить режим обновления
        if(mode == "delete") {
            params.ACTION = "delete";
            productQuantity = 0;
        } else if(mode == "plus") {
            params.ACTION = "ADD2BASKET";
            productQuantity++;
        } else if(mode == "minus") {
            productQuantity--;
        }

        params.quantity = productQuantity;
        if(params.quantity == 0) {
            params.ACTION = "delete";
            this.deleteBasketItem = params.id;
        }
        this.updateQuantityInput(params.quantity);

        this.basketTable.loading = new L.LocalWaitWindow(this.basketTable);
        var sendObj = new L.Ajax(this._url, params, this.update_callback, this);
        sendObj.send();
//alert(this._url);
        return false;
    },
    update_callback: function(data, caller) {
        caller.basketTable.loading.remove();

        if(data.ERROR) {
            if(data.ERROR == "OUT_OF_STOCK") {
                alert("На складе всего " + data.QUANTITY + " шт.");
                caller.updateQuantityInput(data.TOTAL_IN_BASKET);
            }
        } else {
            caller.updatePrice(data);
        }

        if(caller.deleteBasketItem)
            caller.removeBasketItem();
    },
    updateQuantityInput: function(newQuantity) {
        this.quantityInput.val(newQuantity);
    },
    updatePrice: function(data) {
        if(data.TOTAL_COUNT > 0) {
            if(!this.deleteBasketItem) {
                // обновить стоимость позиции в корзине
                this.parentRow.find("td.total-price h5").text(data.ITEMS[this.productID]["TOTAL_PRICE_RUB_FORMATED"]);
                this.parentRow.find("td.total-price h6").text(data.ITEMS[this.productID]["TOTAL_PRICE_FORMATED"]);
            }
            // обновить общую стоимость товаров в корзине
            this.basketTable.find("tfoot .total_price").html(data.TOTAL_SUM_RUB + "<span>" + data.TOTAL_SUM + "</span>");
        } else {
            this.removeBasket();
        }

        // обновить общую стоимость и количество в малой корзине
        new L.BasketSmall(data.TOTAL_COUNT, data.TOTAL_SUM_RUB);
    },
    removeBasketItem: function() {
        if(this.deleteBasketItem) {
            if(this.parentRow)
                this.parentRow.fadeOut("quick", function(){$(this).remove()});
            delete this.deleteBasketItem;
        }
    },
    removeBasket: function() {
        $(".basket_page").html("<div class='b-info-msg alert alert-success'><span>В вашей корзине еще нет товаров.</span></div>");
    },
    events: function() {
        var parentObj = this;
        this.delButton.on("click", function(){
            parentObj.update(this, "delete");
            return false;
        });

        this.quantityInput.on("change", function(e){
            parentObj.update(this, "change");
            e.stopPropagation();
            return false;
        });

        this.minusButton.on("click", function(e) {
            parentObj.update(this, "minus");
            return false;
        });

        this.plusButton.on("click", function(e) {
            parentObj.update(this, "plus");
            return false;
        });
    }
});

$(document).ready(function(){
    new L.BasketPage();
});