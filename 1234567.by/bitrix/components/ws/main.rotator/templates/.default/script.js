L.BannerRotator = L.Class.extend({
    initialize: function(rotatorObj, navObj, prevArr, nextArr){
        this.rotatorObj = rotatorObj;
        this.rotatorObj.find("div.item").width(this.rotatorObj.width());
        $(".slider-container").css({
            height: this.rotatorObj.height() + "px",
            width: (this.rotatorObj.width() * this.rotatorObj.find("div.item").length) + "px"
        });

        this.navObj = navObj;
        this.prevArr = prevArr;
        this.nextArr = nextArr;

        this.loadedImages = 0;
        this.totalImages = 0;

        this.events();
    },
    slide: function(forward, bannerID) {
        var currentBanner = this.rotatorObj.find("div.active"),
            nextBanner, blockOffset = currentBanner.width();
        if(forward === 1) {
            if(this.autoSliding)
            {
                if(!this.direction)
                    this.direction = 1;
                if(currentBanner.hasClass("last-element"))
                    this.direction = -1;
                else if (currentBanner.hasClass("first-element"))
                    this.direction = 1;

                if(this.direction > 0)
                    nextBanner = currentBanner.next();
                else
                    nextBanner = currentBanner.prev();

            } else {
                if(currentBanner.hasClass("last-element"))
                    nextBanner = $("div.first-element");
                else
                    nextBanner = currentBanner.next();
            }
        } else if(forward === 0) {
            if(currentBanner.hasClass("first-element"))
                nextBanner = $("div.last-element");
            else
                nextBanner = currentBanner.prev();
        }

        if(!bannerID && nextBanner) {
            bannerID = nextBanner.attr("id").replace("banner", "");
        }
        else
            nextBanner = $("#banner" + bannerID);

        nextBanner.addClass("active").show();
        blockOffset = nextBanner.index() * blockOffset;
        if(blockOffset > 0)
            blockOffset = "-" + blockOffset + "px";

        this.rotatorObj.find(".item-container").animate({
            marginLeft: blockOffset
        }, 500);
        this.rotatorObj.find(".item").removeClass("active"); //.hide();
        if(bannerID) {
            nextBanner.addClass("active").show();
            this.navObj.find("li.selected").removeClass("selected");
            this.navObj.find("li[rel='" + bannerID + "']").addClass("selected");
        }
    },
    startAutoSlide: function() {
        var parentObj = this;
        this.autoSliding = setInterval(function(){
            parentObj.slide(1);
        }, 10000);
    },
    stopAutoSlide: function() {
        if(this.autoSliding) {
            clearInterval(this.autoSliding);
            this.autoSliding = false;
        }
        return false;
    },
    preloadImages: function() {
        var arImages = [],
            rotatorImages = $(this.rotatorObj).find("img"),
            parentObj = this;

        this.totalImages = rotatorImages.length;
        rotatorImages.each(function(){
            var rotatorImg = new Image();
            rotatorImg.src = $(this).attr("src");
            rotatorImg.onload = parentObj.preloadImageComplete();
        });
    },
    preloadImageComplete: function(){
        this.loadedImages++;

        if(this.loadedImages == this.totalImages) {
            this.rotatorObj.find("a").css("visibility", "visible");
            this.rotatorObj.css("background", "none");
            this.rotatorObj.find("a").animate({opacity: 1}, 1000);
            this.startAutoSlide();
        }
    },
    events: function(){
        var parentObj = this;
        this.nextArr.on("click", function(e){
            parentObj.stopAutoSlide();

            parentObj.slide(1);
            e.stopImmediatePropagation();
            return false;
        });
        this.prevArr.on("click", function(e){
            parentObj.stopAutoSlide();

            parentObj.slide(0);
            e.stopImmediatePropagation();
            return false;
        });

        this.navObj.find("li").on("click", function(e){
            parentObj.stopAutoSlide();

            var bannerID = $(this).attr("rel");
            parentObj.slide(false, bannerID);
            e.stopImmediatePropagation();
            return false;
        });
        this.preloadImages();
    }
});

$(document).ready(function(){
    new L.BannerRotator($(".slider"), $(".controls"), $(".slider div.prev"), $(".slider div.next"));
});