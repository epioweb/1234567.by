L.Order = L.Class.extend({
    initialize: function(){
        this._form = $("#ORDER_FORM");
        this._formContent = this._form.find("#order_form_content");
        this._url = this._form.attr("action");
        this.events();
    },
    submit: function() {
        var params;

        params = this._form.serialize();
        var sendObj = new L.Ajax(this._url, params, this.submit_callback, this, "html");
        this.loading = new L.LocalWaitWindow(this._form);
        sendObj.send();
    },
    submit_callback: function (data, caller) {
        caller.loading.remove();

        if(data) {
            if(data.TYPE != "ERROR")
                caller._formContent.hide().html(data);
            else
                caller._formContent.hide().html(data.MESSAGE);
        }
    },
    complete: function() {
        var parentObj = this;
        $("h1").fadeOut();
        this._form.html(this._formContent.html()).hide();

        $(".basket_table").fadeOut(function(){
            $(this).remove();
            parentObj._form.show();
            parentObj._formContent.remove();
        });
        this.scrollTop();
    },
    incomplete: function() {
        this._formContent.fadeIn();
    },
    scrollTop: function() {
        $('html, body').animate({
            scrollTop: $(".header").offset().top
        }, 400);
    },
    events: function(){
        var parentObj = this;
        this._form.on("submit", function(){
            parentObj.submit();
            return false;
        });
    }
});

var lOrder = {};
$(document).ready(function(){
    lOrder = new L.Order;
});


//Показ/сокрытие поля для ввода email
$(function(){
   if($('input:radio[id=ID_PAY_SYSTEM_ID_1]').prop("checked") || ($('input:radio[id=ID_PAY_SYSTEM_ID_1]').length==0)) {
      $('input[id=ORDER_PROP_5]').prev('label').hide();
      $('input[id=ORDER_PROP_5]').hide();
   }
   $('input:radio[id=ID_PAY_SYSTEM_ID_1]').change(function() {
       $('input[id=ORDER_PROP_5]').prev('label').hide();
       $('input[id=ORDER_PROP_5]').hide();
   });

   $('input:radio[name=PAY_SYSTEM_ID]').not("[id='ID_PAY_SYSTEM_ID_1']").change(function() {
       $('input[id=ORDER_PROP_5]').prev('label').show();
       $('input[id=ORDER_PROP_5]').show();
   });
});