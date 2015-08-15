<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {
    Battle = function(canvas, data) {
        this.stage = new createjs.Stage(canvas);
        this.data = data;
        this.combatants = {};
        this.field = [64,40];
        this.current = 0;
        this.idle = 500;

        this.card = null;
        this.card_target = null;

        this.ressources = [];
        this.waiting = false;
        this.loadstate = 0;

        var alias = this;
        createjs.Ticker.setFPS(60);
        createjs.Ticker.addEventListener("tick", function() {
            alias.performZSorting();
        });
        createjs.Ticker.addEventListener("tick", this.stage);

    };

    Battle.prototype.initUI = function() {
        var background = new createjs.Shape();
        background.graphics.beginBitmapFill(this.getResource('field.png')).drawRect(0,0,640,400);
        background.z = -100;
        this.stage.addChild(background);
    };

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

    Battle.prototype.proceed = function(factor) {
        if (this.loadstate) {
            this.waiting = true;
            return;
        }

        if (factor === undefined)
            factor = 1;

        var event = this.data[this.current++];
        if (!event) return;
        var type = event[0];

        var tmp = [], i = 1, c;
        while (c = event[i++])
            tmp.push(c);
        event = tmp;

        var alias = this;
        if (alias.events[type])
            window.setTimeout(function() {
                console.log(type, event);
                alias.events[type].apply(alias, event);
            }, this.idle * factor);
        else alias.proceed();
    }
})();