(function(a) {
    a.L = {
        VERSION : "0.1",
        noConflict : function() {
            a.L = this._originalL;
            return this
        },
        _originalL : a.L
    }
})(this);
L.Util = {
    extend : function(a) {
        var b = Array.prototype.slice.call(arguments, 1);
        for (var c = 0, d = b.length, e; c < d; c++) {
            e = b[c] || {};
            for (var f in e) {
                if (e.hasOwnProperty(f)) {
                    a[f] = e[f]
                }
            }
        }
        return a
    },
    bind : function(a, b) {
        return function() {
            return a.apply(b, arguments)
        }
    },
    formatNum : function(a, b) {
        var c = Math.pow(10, b || 5);
        return Math.round(a * c) / c
    },
    setOptions : function(a, b) {
        a.options = L.Util.extend({}, a.options, b)
    },
    getParamString : function(a) {
        var b = [];
        for (var c in a) {
            if (a.hasOwnProperty(c)) {
                b.push(c + "=" + a[c])
            }
        }
        return "?" + b.join("&")
    },
    ce: function(el, contaner, id, className, style){
        var elObj = {};
        elObj = document.createElement(el);
        if(className)
            elObj.className = className;
        if(id)
            elObj.setAttribute("id", id);

        if(style)
            elObj.setAttribute("style", style);

        if(typeof contaner === "object")
            contaner.appendChild(elObj);
        return elObj;
    },
    getBodyScrollTop: function()
    {
        return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
    },
    getBodyScrollLeft: function()
    {
        return self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft);
    }
};

/**
 * Расширение класса Массивов.
 * Проверка существования значения в массиве
 */
Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
}

L.Class = function() {
};
L.Class.extend = function(a) {
    var b = function() {
        if (this.initialize) {
            this.initialize.apply(this, arguments)
        }
    };
    var c = function() {
    };
    c.prototype = this.prototype;
    var d = new c;
    d.constructor = b;
    b.prototype = d;
    b.superclass = this.prototype;
    for (var e in this) {
        if (this.hasOwnProperty(e) && e !== "prototype" && e !== "superclass") {
            b[e] = this[e]
        }
    }
    if (a.statics) {
        L.Util.extend(b, a.statics);
        delete a.statics
    }
    if (a.includes) {
        L.Util.extend.apply(null, [d].concat(a.includes));
        delete a.includes
    }
    if (a.options && d.options) {
        a.options = L.Util.extend({}, d.options, a.options)
    }
    L.Util.extend(d, a);
    b.extend = L.Class.extend;
    b.include = function(a) {
        L.Util.extend(this.prototype, a)
    };
    return b
};

/******************************************************************
 * 					Ajax
 ******************************************************************/
L.Ajax = L.Class.extend({
    initialize: function(url, params, callback, caller, dataType){
        this._url 		= url;
        this._params 	= params;
        this._callback 	= callback;
        this._caller	= caller;

        this._dataType = "json";
        if(dataType)
            this._dataType = dataType;

    },
    send: function(){
        var parentObj = this;
        $.ajax({
            type: 		"POST",
            url: 		parentObj._url,
            data: 		parentObj._params,
            dataType: 	this._dataType
        }).done(function(data){
                if(typeof(data.ERROR) != "undefined")
                {
                    data.TYPE = "ERROR";
                    data.MESSAGE = data.ERROR;
                }
                if(typeof(data.TYPE) == "undefined")
                    data.TYPE = "OK";

                parentObj._callback(data, parentObj._caller);
            }).fail(function(jqXHR, textStatus) {
                response = {
                    "TYPE"		: "ERROR",
                    "MESSAGE"	: jqXHR.statusText,
                    "HTML"      : jqXHR.responseText
                }
                parentObj._callback(response, parentObj._caller);
            });
    }
});

/******************************************************************
 * 					Корзина
 ******************************************************************/
L.Basket = L.Class.extend({
    initialize: function(productID, priceID, quantity, callback, caller){
        this._callback = callback;
        this._productID = productID;
        this._caller = caller;
        this._quantity = quantity;
        this._priceID = priceID;
        this._action = "ADD2BASKET";
    },
    add: function(){
        if(this._quantity == 0)
            this._action = "DELETE";

        var params = {
            "ACTION"	: this._action,
            "id"		: this._productID,
            "quantity" 	: this._quantity,
            "PRICE_ID"  : this._priceID
        };
        var callObj = this;
        var sendObj = new L.Ajax("/tools/add_to_basket.php", params, this.callback, callObj);
        sendObj.send();
    },
    callback: function(data, callObj){
        if(data.TYPE == "ERROR")
        {
            if(typeof(data.MESSAGE) == "undefined")
                data.MESSAGE = "Ошибка";
        }
        else
        {
            if(typeof(data.MESSAGE) == "undefined")
            {
                if(callObj._action == "ADD2BASKET")
                    data.MESSAGE = "Добавлено<br/><i>Перейти к <a href='" + data.PATH_TO_BASKET + "'>оформлению</a></i>";
                else if(callObj._action == "DELETE")
                    data.MESSAGE = "Удалено";
            }
        }

        callObj._updateSmallBasket(data);
        callObj._callback(data, callObj._caller);
    },
    _updateSmallBasket: function(data){
        bs = new L.BasketSmall(data.TOTAL_COUNT, data.TOTAL_SUM_RUB);
    }
});

/**
 * Малая корзина
 */
L.BasketSmall = L.Class.extend({
    initialize: function(count, price){
        this.activeClass = "full";
        this.contaner = $('a.basket');

        // если установлено количество, обновляем его

        if(typeof(count) == "undefined")
            this.count = -1;
        else
            this.count = parseInt(count);

        if(price != null && typeof(price) != "undefined")
            this.price = price;
        else
            this.price = 0;

        this.setTotalCount();
        this.setTotalPrice();
    },
    setTotalCount: function(){
        if(this.count == 0)
            this.contaner.removeClass(this.activeClass);
        else if(!this.contaner.hasClass(this.activeClass))
            this.contaner.addClass(this.activeClass);

        if(this.count >= 0)
            this.contaner.find("span").eq(0).text(this.count);
    },
    setTotalPrice: function() {
        if(this.price !== false) {
            this.contaner.find("span").eq(1).text(this.price);
        }
    }
});

/******************************************************************
 * 					Уведомления "Вплывающее уведомление"
 ******************************************************************/
L.SmallNotice = L.Class.extend({
    initialize : function(contaner, message, autoremove, left) {
        this._contaner = contaner;
        if(message != "preloader")
            this._message  = message;
        else
            this._message  = this.loader;

        this._autoremove = autoremove;
        this._timeOut = 2000;
        if(typeof(left) === "undefined")
            this._left = "0";
        else
            this._left = left;

        this.initLayout();

    },
    initLayout: function(){
        this._wrapper = document.createElement("div");
        this._wrapper.className = "added-info";
        this._wrapper.style.left = this._left;

        this._wrapper_contaner = document.createElement("span");
        this._wrapper_contaner.innerHTML = this._message;
        this._wrapper_contaner.style.lineHeight = "normal";

        this._wrapper.appendChild(this._wrapper_contaner);

        $(this._contaner).css("position", "relative");
        $(this._contaner).append(this._wrapper);

        if(this._autoremove)
        {
            var localParent = this;
            setTimeout(function(a){localParent.remove()}, this._timeOut);
        }
    },
    setContent: function(message){
        this._message = message;
    },
    delayRemove: function(timeout){
        var localParent = this;
        if(typeof(timeout) == "undefined")
            timeout = this._timeOut;
        setTimeout(function(a){localParent.remove()}, timeout);
    },
    remove: function()
    {
        $(this._wrapper).fadeOut(300, function(){
            $(this).remove();
        });
        $(this._contaner).css("position", "auto");
    },
    update: function(message){
        this.setContent(message);
        this._wrapper_contaner.innerHTML = this._message;
    },
    loader: "<img src='/images/loader-s.gif' />"
});

/**
 * Окно ожидания ответа
 * @type {*}
 */
L.LocalWaitWindow = L.Class.extend({
    initialize: function(contaner, message){
        this._contaner = contaner;
        if(message)
            this._message = message;
        else
            this._message = "";

        this.initLayout();
    },
    initLayout: function(){
        this._wrapper = document.createElement("div");
        this._wrapper.className = "wait-window";
        $(this._wrapper).css({
            height: "100%",
            width: "100%",
            background: "#fff",
            position: "absolute",
            top: "0",
            textAlign: "center",
            opacity: 0.5
        });
        if(this._message.length == 0) {
            $(this._wrapper).css("background", "url(/bitrix/templates/.default/ajax/images/wait.gif) no-repeat center center #fff");
        }

        this._wrapper_contaner = document.createElement("div");
        this._wrapper_contaner.innerHTML = this._message;

        this._wrapper.appendChild(this._wrapper_contaner);

        $(this._contaner).css("position", "relative");
        $(this._contaner).append(this._wrapper);
    },
    remove: function()
    {
        $(this._wrapper).fadeOut(300, function(){
            $(this).remove();
        });
        $(this._contaner).css("position", "auto");
    }
});

/******************************************************************
 *                  Простое модальное окно
 ******************************************************************/
L.SmallModalWin = new L.Class.extend({
    initialize: function(title, message, onclose, caller, className, id) {
        if(onclose) {
            this._onClose = onclose;
            this._caller = caller;
        }
        this._title = title;
        this._message = message;
        this._class = className ? className : "";
        this._id = id ? id : "";

        this.createLayout();
        this.open();
        this.events();
    },
    createLayout: function() {
        this._wrapper = L.Util.ce("div", false, this._id, "popup " + this._class);

        this._rightBlockClose = L.Util.ce("a", this._wrapper, false, "close-popup");
        this._rightBlockClose.innerHTML = "&#215;";
        this._wrapperContainer = L.Util.ce("div", this._wrapper);

        this._header = L.Util.ce("h1", this._wrapperContainer);
        this._header.innerHTML = this._title;

        this._content = L.Util.ce("div", this._wrapperContainer, false, "popup-content");
        this.setContent(this._message);
        this._content.style.marginBottom = "14px";

        $("body").append(this._wrapper);
    },
    open: function() {
        $(this._wrapper).show();
        this.center();
    },
    close: function() {
        if(this._onClose)
            this._onClose(this._caller);

        if(this._wrapper) {
            $(this._wrapper).remove();
            delete this._wrapper;
        }
    },
    setContent: function(content) {
        this._message = content;
        this._updateContent();
    },
    _updateContent: function() {
        this._content.innerHTML = this._message;
    },
    events: function() {
        var parentObj = this;
        $(this._rightBlockClose).click(function(e){
            parentObj.close();
            e.stopPropagation();
        });
        if(this._bottomClose) {
            $(this._bottomClose).click(function(e){
                parentObj.close();
                e.stopPropagation();
            });
        }
        $(document).keyup(function(e){
            if(e.keyCode == 27)
            {
                parentObj.close();
                e.stopPropagation();
            }
        });

        $(window).resize(function(){
            parentObj.center();
        });
    },
    center: function(){
        var windowWidth = $(window).width(),
            windowHeight = $(window).height(),
            popupHeight = $(this._wrapper).height(),
            popupWidth = $(this._wrapper).width();
        $(this._wrapper).css({
            "top": windowHeight / 2 - (popupHeight / 2) - 30,
            "left": windowWidth / 2 - (popupWidth / 2)
        });
    }
});