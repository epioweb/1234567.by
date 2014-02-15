(function($){$.fn.keyboard=function(){$k.bind(this,arguments);return this;};$.keyboard=function(){$k.bind($(document),arguments);return this;};var $k={setup:{"strict":true,"event":"keydown","preventDefault":false},keys:{cont:[],getCodes:function(){var codes=[];for(var i=0;i<$k.keys.cont.length;i++){codes.push($k.keys.cont[i].keyCode);}return codes;},add:function(e){if(e.keyCode==0){}else{$k.keys.rm(e);$k.keys.cont.push(e);$k.keys.dump();}},rm:function(e){for(var i=0;i<$k.keys.cont.length;i++){if($k.keys.cont[i].keyCode==e.keyCode){$k.keys.cont.splice(i,1);return;}}},clear:function(){$k.keys.cont=[];},dump:function(){}},keyCodes:{a:65,b:66,c:67,d:68,e:69,f:70,g:71,h:72,i:73,j:74,k:75,l:76,m:77,n:78,o:79,p:80,q:81,r:82,s:83,t:84,u:85,v:86,w:87,x:88,y:89,z:90,n0:48,n1:49,n2:50,n3:51,n4:52,n5:53,n6:54,n7:55,n8:56,n9:57,tab:9,enter:13,shift:16,backspace:8,ctrl:17,alt:18,esc:27,space:32,menu:93,pause:19,cmd:91,insert:45,home:36,pageup:33,"delete":46,end:35,pagedown:34,f1:112,f2:113,f3:114,f4:115,f5:116,f6:117,f7:118,f8:119,f9:120,f10:121,f11:122,f12:123,np0:96,np1:97,np2:98,np3:99,np4:100,np5:101,np6:102,np7:103,np8:104,np9:105,npslash:11,npstar:106,nphyphen:109,npplus:107,npdot:110,capslock:20,numlock:144,scrolllock:145,equals:61,hyphen:109,coma:188,dot:190,gravis:192,backslash:220,sbopen:219,sbclose:221,slash:191,semicolon:59,apostrophe:222,aleft:37,aup:38,aright:39,adown:40},parseArgs:function(args){if(typeof args[0]=="object"){return{setup:args[0]};}else{var secondIsFunc=(typeof args[1]=="function");var isDelete=!secondIsFunc&&(typeof args[2]!="function");var argsObj={};argsObj.keys=args[0];if($.isArray(argsObj.keys)){argsObj.keys=argsObj.keys.join(" ");}if(isDelete){argsObj.isDelete=true;}else{argsObj.func=secondIsFunc?args[1]:args[2];argsObj.cfg=secondIsFunc?args[2]:args[1];if(typeof argsObj.cfg!="object"){argsObj.cfg={};}argsObj.cfg=$.extend(clone($k.setup),argsObj.cfg);}return argsObj;}},getIndex:function(keyCodes,order){return(order=="strict")?"s."+keyCodes.join("."):"f."+clone(keyCodes).sort().join(".");},getIndexCode:function(index){if($k.keyCodes[index]){return $k.keyCodes[index];}else{throw"No such index: «"+index+"»";}},getRange:function(title){var c=$k.keyCodes;var f=arguments.callee;switch(title){case"letters":return range(c["a"],c["z"]);case"numbers":return range(c["n0"],c["n9"]);case"numpad":return range(c["np0"],c["np9"]);case"fkeys":return range(c["f1"],c["f12"]);case"arrows":return range(c["aleft"],c["adown"]);case"symbols":return[c.equals,c.hyphen,c.coma,c.dot,c.gravis,c.backslash,c.sbopen,c.sbclose,c.slash,c.semicolon,c.apostrophe,c.npslash,c.npstar,c.nphyphen,c.npplus,c.npdot];case"allnum":return f("numbers").concat(f("numpad"));case"printable":return f("letters").concat(f("allnum").concat(f("symbols")));default:throw"No such range: «"+title+"»";}},stringGetCodes:function(str){var parts;str=str.toLowerCase();if(str.match(/^\[[\w\d\s\|\)\(\-]*\]$/i)){var codes=[];parts=str.substring(1,str.length-1).replace(/\s/,"").split("|");for(var i=0;i<parts.length;i++){var p=$k.stringGetCodes(parts[i]);codes=codes.concat(p);}return codes;}else{if(str.match(/^\([\w\d\s\-]*\)$/i)){parts=str.substring(1,str.length-1).replace(/\s/,"").split("-");if(parts.length==2){return range($k.getIndexCode(parts[0]),$k.getIndexCode(parts[1]));}else{return $k.getRange(parts[0]);}}else{return[$k.getIndexCode(str)];}}},getCodes:function(keys){var keycodes=[];for(var i=0;i<keys.length;i++){var key=keys[i];if(!isNaN(key)){key=[1*key];}else{if(typeof key=="string"){key=$k.stringGetCodes(key);}else{throw"Wrong key type: «"+(typeof key)+"»";}}keycodes.push(key);}return keycodes;},parseKeysString:function(str){var parts=str.split(",");for(var i=0;i<parts.length;i++){var string=$.trim(parts[i]);parts[i]={};parts[i].order=string.indexOf("+")>=0?"strict":"float";parts[i].codes=$k.getCodes(string.split(parts[i].order=="strict"?"+":" "));parts[i].index=$k.getIndex(parts[i].codes,parts[i].order);parts[i].group=i;}return parts;},match:function(bind){var k,i,matched,cur=undefined;var cont=$k.keys.getCodes();var codes=clone(bind.keys.codes);var eventIndexes=[];if(codes.length==0){return false;}if(bind.keys.order=="strict"){for(i=0;i<cont.length;i++){if(!codes.length){break;}if(cur===undefined){cur=codes.shift();}if(inArray(cont[i],cur)){cur=undefined;eventIndexes.push(i);}else{if(bind.cfg.strict){return false;}}}return(codes.length===0&&cur===undefined)?eventIndexes:false;}else{for(i=0;i<codes.length;i++){matched=false;for(k=0;k<codes[i].length;k++){cur=$.inArray(codes[i][k],cont);if(cur>=0){eventIndexes.push(cur);matched=true;break;}}if(!matched){return false;}}if(bind.cfg.strict){for(i=0;i<cont.length;i++){matched=false;for(k in codes){if(inArray(cont[i],codes[k])){matched=true;break;}}if(!matched){return false;}}}return eventIndexes;}},hasCurrent:function(bind,e){var last=bind.keys.codes.length-1;return(bind.keys.order=="strict")?inArray(e.keyCode,bind.keys.codes[last]):inArrayR(e.keyCode,bind.keys.codes);},checkBinds:function($obj,e){var ei,okb=$obj.keyboardBinds;for(var i in okb){var bind=okb[i];if(bind.cfg.event==e.originalEvent.type){ei=$k.match(bind);if(ei&&$k.hasCurrent(bind,e)){var backup=$obj.keyboardFunc;var events=[];for(var k in ei){events.push($k.keys.cont[ei[k]]);}$obj.keyboardFunc=bind.func;$obj.keyboardFunc(events,bind);$obj.keyboardFunc=backup;if(bind.cfg.preventDefault){e.preventDefault();}}}}},bind:function($obj,args){args=$k.parseArgs(args);if(args.setup){$k.setup=$.extend($k.setup,args.setup);}else{if(!$obj.keyboardBinds){$obj.keyboardBinds={};$obj.keydown(function(e){$k.keys.add(e);$k.checkBinds($obj,e);}).keyup(function(e){$k.checkBinds($obj,e);});}var parts=$k.parseKeysString(args.keys);for(var i=0;i<parts.length;i++){if(args.keys.isDelete){$obj.keyboardBinds[parts[i].index]=undefined;}else{$obj.keyboardBinds[parts[i].index]=clone(args);$obj.keyboardBinds[parts[i].index].keys=parts[i];}}}},init:function(){$(document).keydown($k.keys.add).keyup(function(e){setTimeout(function(){$k.keys.rm(e);},0);}).blur($k.keys.clear);}};var inArrayR=function(value,array){for(var i=0;i<array.length;i++){if(typeof array[i]=="object"||$.isArray(array[i])){if(inArrayR(value,array[i])){return true;}}else{if(value==array[i]){return true;}}}return false;};var inArray=function(value,array){return($.inArray(value,array)!=-1);};var range=function(from,to){var r=[];do{r.push(from);}while(from++<to);return r;};var clone=function(obj){var newObj,i;if($.isArray(obj)){newObj=[];for(i=0;i<obj.length;i++){newObj[i]=(typeof obj[i]=="object"||$.isArray(obj[i]))?clone(obj[i]):obj[i];}}else{newObj={};for(i in obj){newObj[i]=(typeof obj[i]=="object"||$.isArray(obj[i]))?clone(obj[i]):obj[i];}}return newObj;};$k.init();})(jQuery);

L.Orphus = L.Class.extend({
    initialize: function() {
        this.minLength = 2;
        this.maxLength = 512;
        this.formObj = $(".orphus-form").html();
        this.url = "/bitrix/components/ws/orphus/send.php";
        this.message = [
            "Слишком длинный текст.\nВыделите, пожалуйста, более короткую часть текста и повторите попытку",
            "Сообщить об ошибке",
            "Сообщение успешно отправлено.\nСпасибо!"
        ];
        this.events();
    },
    showPopup: function() {
        if(!this.popup) {
            var selectedText = this.getSelectedText();
            if(selectedText.length < this.minLength)
                return false;

            if(selectedText.length > this.maxLength) {
                alert(this.message[0]);
            } else {
                this.popup = new L.SmallModalWin(this.message[1], this.formObj, this.onclose, this);
                $(this.popup._content).find(".preview").html("..." + selectedText + "...");
                this.bindEvents();
            }
        }

        return true;
    },
    onclose: function(caller) {
        if(caller.popup) {
            if(caller.loading) {
                caller.loading.remove();
                delete caller.loading;
            }

            delete caller.popup;
        }
    },
    send: function() {
        var send, params;
        params = {
            send: 'Y',
            comment:        $(this.popup._content).find('.send-note-window .preview').text(),
            text:           $(this.popup._content).find('.send-note-window #send-note-textarea').val(),
            page:           self.location.href,
            captcha_word:   $(this.popup._content).find('.send-note-window input[name=captcha_word]').val(),
            captcha_sid:    $(this.popup._content).find('.send-note-window input[name=captcha_code]').val()
        };

        this.loading = new L.LocalWaitWindow($(this.popup._content));
        send = new L.Ajax(this.url, params, this.send_callback, this);
        send.send();
    },

    bindEvents: function() {
        var parentObj = this;
        $(this.popup._content).find("form").submit(function(){
            parentObj.send();
            return false;
        });

        $(this.popup._content).find('.capch_img, .refresh-captcha').click(function(){
            $(parentObj.popup._content).find('.capch_img').css("opacity", 0.5);

            $.post('/bitrix/components/ws/orphus/captcha.php',function(e){
                $(parentObj.popup._content).find('.send-note-window input[name=captcha_word]').val('');
                $(parentObj.popup._content).find('.send-note-window input[name=captcha_code]').val(e);
                $(parentObj.popup._content).find('.capch_img').attr({ src: '/bitrix/tools/captcha.php?captcha_code=' + e });

                $(parentObj.popup._content).find('.capch_img').css("opacity", 1);
            });

            return false;
        });

        $(this.popup._content).find("input[name='cancel-button']").click(function(){
            parentObj.popup.close();
        });
    },
    send_callback: function(data, caller) {
        caller.loading.remove();
        if(data.ERROR) {
            alert(data.ERROR);
        } else if(data.TYPE){
            if(data.TYPE == "OK") {
                alert(caller.message[2]);
                caller.popup.close();
            }
        }
    },
    events: function() {
        var parentObj = this;

        $(document).keyboard('ctrl enter', function() {
            parentObj.showPopup();
        });
    },
    getSelectedText: function() {
        var selected = '';
        if (window.getSelection) { selected = window.getSelection(); }
        else if (document.getSelection){ selected = document.getSelection(); }
        else if (document.selection){ selected = document.selection.createRange().text;}
        return selected.toString();
    }
});

$(document).ready( function() {
    var orphus = new L.Orphus();
});