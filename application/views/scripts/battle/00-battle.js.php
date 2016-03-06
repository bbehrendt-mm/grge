<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {
    Battle = function(canvas, data) {
        var alias = this;
        CanvasAnimationModule.call(this, canvas, 60, 'media/icons/battle/',
            function() {alias.performZSorting();},
            function() {alias.initUI(); alias.proceed();}
        );

        this.data = data;
        this.combatants = {};
        this.field = [64,40];
        this.current = 0;
        this.idle = 500;

        this.splatter_count = 2;
        this.splatlayer = null;

        this.card = null;
        this.card_target = null;

        this.start_timeout = 0;
    };

    Battle.prototype = Object.create(CanvasAnimationModule.prototype);

    Battle.prototype.checkNewChar = function(id, set_false) {
        if (set_false)
            return this.combatants[id]['new'] = false;
        else return this.combatants[id]['new'];
    };

    Battle.prototype.initUI = function() {
        var background = new createjs.Shape();
        background.graphics.beginBitmapFill(this.getResource('field.png')).drawRect(0,0,640,400);
        background.z = -100;
        this.stage.addChild(background);

        var grunge = this.getResource('atmosphere.png');
        var grunge_layer = new createjs.Bitmap(grunge);
        grunge_layer.scaleX = 650/grunge.width;
        grunge_layer.scaleY = 410/grunge.height;
        grunge_layer.x = grunge_layer.y = -5;
        grunge_layer.z = -99;
        grunge_layer.filters = [new createjs.BlurFilter(3, 3, 1)];
        grunge_layer.cache(0,0,650,410,1);

        this.stage.addChild(grunge_layer);

        this.splatlayer = new createjs.Container();
        this.splatlayer.z = -99;
        this.stage.addChild(this.splatlayer);
        this.splatlayer.cache(0,0,640,400,1);
    };

    Battle.prototype.events = {};

    Battle.prototype.transform = function(pos) {
        return {x: Math.round(pos.x * 9) + 32,y: Math.round(pos.y * 9) + 20};
    };

    Battle.prototype.performZSorting = function() {
        this.stage.sortChildren(function(a, b) {
            return a.z - b.z;
        })
    };

    Battle.prototype.setDelay = function(delay) {
        this.idle = delay;
    };

    Battle.prototype.pause = function() {
        createjs.Ticker.paused = true;
    };

    Battle.prototype.unpause = function() {
        createjs.Ticker.paused = false;
    };

    Battle.prototype.reset = function() {
        createjs.Tween.removeAllTweens();
        window.clearTimeout(this.start_timeout);
        this.current = 0;
        this.combatants = {};
        this.card = null;
        this.card_target = null;

        this.stage.removeAllChildren();
        this.begin();
    };

    Battle.prototype.finish = function() {
        var alias = this;
        createjs.Tween.get(this.stage)
            .wait(this.idle * 2)
            .call(function() {
                alias.showActorCard(false);
            })
            .wait(2000)
            .call(function() {
                alias.callback_finish();
            })
    };

    Battle.prototype.proceed = function(factor) {
        if (this.loadstate) return;

        if (factor === undefined)
            factor = 1;

        var event = this.data[this.current++];
        if (!event) {
            this.finish();
            return;
        }
        var type = event[0];

        var tmp = [], i = 1, c;
        while ((c = event[i++]) !== undefined)
            tmp.push(c);
        event = tmp;

        var alias = this;
        if (alias.events[type])
            alias.start_timeout = window.setTimeout(function() {
                alias.events[type].apply(alias, event);
            }, this.idle * factor);
        else alias.proceed();
    }
})();