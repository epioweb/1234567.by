function blinkEye() {
    if(moveEyeLidTimer)
        clearInterval(moveEyeLidTimer);

    moveEyeLidTimer = setInterval(moveEyeLid, 30);
    return false;
}

function moveEyeLid() {
    this.eyeContainer = $(".header .smile .eye");
    if(!this.eyeSemiCloseImg) {
        this.eyeSemiCloseImg = new Image();
        this.eyeSemiCloseImg.src = "/i/eyes_semiclosed.jpg";

        this.eyeCloseImg = new Image();
        this.eyeCloseImg.src = "/i/eyes_closed.jpg";
    }

    this.eyeStates  = [
        "",
        this.eyeSemiCloseImg,
        this.eyeCloseImg
    ];

    // working
    if(this.pause) {
        if(this.pauseCounter >= 15) {
            this.pauseCounter = false;
            this.pause = false;
        } else {
            this.pauseCounter++;
            return true;
        }
    }

    if(!this.counter) {
        this.counter = 0;
        this.forward = 1;
    }
    if(this.counter > 1) {
        this.forward = 0;
    }

    if(this.forward) {
        this.counter++;
        if(this.counter == 1)
            this.eyeContainer.show();
    }
    else {
        this.counter--;

        // останавливаем моргание
        if(this.counter === 0) {
            clearInterval(moveEyeLidTimer);
            // прячем контейнер с моргающим глазом
            this.eyeContainer.hide();
        }
    }

    if(this.counter > 0) {
        this.eyeContainer.html("<img src='" + this.eyeStates[this.counter].src + "'/>");

        if(this.counter == 2) {
            // задержка
            this.pause = true;
            this.pauseCounter = 0;
        }
    }
}

var moveEyeLidTimer = false;
$(document).ready(function(){
    setInterval(blinkEye, 4000);
});