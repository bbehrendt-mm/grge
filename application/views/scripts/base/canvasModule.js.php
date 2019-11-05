<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {

    CanvasAnimationModule = function(canvas, fps, ressource_root, pretick_action, onload) {
        this.stage = new createjs.Stage(canvas);

        this.ressources = [];
        this.waiting = false;
        this.loadstate = 0;
        this.loader_leadout = onload;
        this.root = ressource_root[ressource_root.length - 1] == '/' ? ressource_root : (ressource_root + '/');

        this.state = 'created';


        this.callback_start = function() {};
        this.callback_finish = function() {};

        this.globalPlaySpeed = 1;
        this.timelineSpeeds = [];
        this.timelines = {};

        this.pretick = pretick_action;

        createjs.Ticker.framerate = fps;
    };

    CanvasAnimationModule.prototype.rescale = function(h,w,s) {
        this.stage.canvas.height = h;
        this.stage.canvas.width = w;
        this.stage.setTransform(0,0,s,s)
        this.stage.update();
    };

    CanvasAnimationModule.prototype.end = function() {
        createjs.Ticker.removeEventListener("tick", this.pretick);
        createjs.Ticker.removeEventListener("tick", this.stage);
    };

    CanvasAnimationModule.prototype.on = function(event, f) {
        switch (event) {
            case 'start':
                this.callback_start = f;
                break;
            case 'finish':
                this.callback_finish = f;
                break;
        }
        return this;
    };

    CanvasAnimationModule.prototype.begin = function() {
        if (this.pretick) createjs.Ticker.addEventListener("tick", this.pretick);
        var alias = this;
        createjs.Ticker.addEventListener("tick", alias.stage);

        if (this.loadstate)
            this.waiting = true;
        else {
            this.callback_start();
            if (this.loader_leadout) this.loader_leadout();
        }
    };

    CanvasAnimationModule.prototype.queueResource = function(name, path) {
        if (this.ressources[name]) return;

        this.loadstate++;

        this.ressources[name] = new Image();

        var alias = this;
        this.ressources[name].onload = function() {
            alias.loadstate--;
            if (!alias.loadstate && alias.waiting)
                alias.begin();
        };

        if (path.slice(0,2) === '//') path = 'http:' + path;
        this.ressources[name].src = /^(\w*?):\/\//.test(path) ? path : ('<?=URL::base(true)?>' + path);
    };

    CanvasAnimationModule.prototype.addResource = function() {
        for (var i = 0; i < arguments.length; i++) {
            var name = arguments[i];
            this.queueResource(name, '<?=URL::base(true)?>' + this.root + name);
        }
    };

    CanvasAnimationModule.prototype.getResource = function(name) {
        if (this.ressources[name] === undefined)
            console.warn('Attempt to access unknown asset: ' + name);
        return this.ressources[name];
    };

    CanvasAnimationModule.prototype.getAnimation = function(name, sizeX, sizeY) {
        var spriteSheet = new createjs.SpriteSheet({
            images: [this.getResource(name)],
            frames: {width: sizeX, height: sizeY,  count:32, regX: 0, regY:0, spacing:0, margin:0}
        });

        return new createjs.Sprite(spriteSheet);
    };

    CanvasAnimationModule.prototype.createCentralizedBitmapContainer = function(path) {
        var bmp = this.getResource(path);

        var bitmap = new createjs.Bitmap(bmp);
        bitmap.x = -Math.round(bmp.width/2);
        bitmap.y = -Math.round(bmp.height/2);

        var container = new createjs.Container();
        container.addChild(bitmap);
        container.cache(bitmap.x, bitmap.y, bmp.width, bmp.height, 1);

        return container;
    };

    CanvasAnimationModule.prototype.setPlaySpeed = function(value, timeline = "default") {
        if (typeof this.timelines[timeline] === "undefined") return;
        this.timelineSpeeds[timeline] = value;
        var alias = this;

        $.each(this.timelines[timeline].tweens, function(index) {
            alias.timelines[timeline].tweens[index].timeScale = value;
        });
    }

    CanvasAnimationModule.prototype.getPlaySpeed = function(timeline = "default") {
        if (typeof this.timelines[timeline] === "undefined") return this.globalPlaySpeed;
        return this.timelineSpeeds[timeline];
    }

    CanvasAnimationModule.prototype.createTween = function(target, props, timeline = "default") {
        if (typeof this.timelines[timeline] === "undefined") {
            this.timelines[timeline] = new createjs.Timeline({loop: true});
            this.timelineSpeeds[timeline] = this.globalPlaySpeed;
        }

        var tween;
        var alias = this;

        tween = new createjs.Tween(target,props);
        tween.timeScale = this.timelineSpeeds[timeline];

        var default_paused = tween.paused;
        this.timelines[timeline].addTween(tween);
        tween.paused = (typeof default_paused === "undefined") ? false : default_paused;
        tween.addEventListener('complete',function() {alias.timelines[timeline].removeTween(tween);})

        return tween;
    }

    CanvasAnimationModule.prototype.getTimeline = function(timeline = "default") {
        return this.timelines[timeline];
    }
})();