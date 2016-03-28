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

        this.pretick = pretick_action;

        createjs.Ticker.setFPS(fps);
        if (pretick_action) createjs.Ticker.addEventListener("tick", this.pretick);
        createjs.Ticker.addEventListener("tick", this.stage);
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

        this.ressources[name].src = /^(\w*?):\/\//.test(path) ? path : ('<?=URL::base('http')?>' + path);
    };

    CanvasAnimationModule.prototype.addResource = function() {
        for (var i = 0; i < arguments.length; i++) {
            var name = arguments[i];
            this.queueResource(name, '<?=URL::base('http')?>' + this.root + name);
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

})();