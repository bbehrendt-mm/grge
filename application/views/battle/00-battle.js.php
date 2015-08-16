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

        this.state = 'created';

        this.callback_start = function() {};
        this.callback_finish = function() {};

        var alias = this;
        createjs.Ticker.setFPS(60);
        createjs.Ticker.addEventListener("tick", function() {
            alias.performZSorting();
        });
        createjs.Ticker.addEventListener("tick", this.stage);

    };

    Battle.prototype.on = function(event, f) {
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

    Battle.prototype.begin = function() {
        if (this.loadstate)
            this.waiting = true;
        else
            this.callback_start();
            this.proceed();
    };

    Battle.prototype.pause = function() {
        createjs.Ticker.paused = true;
    };

    Battle.prototype.unpause = function() {
        createjs.Ticker.paused = false;
    };

    Battle.prototype.reset = function() {
        createjs.Tween.removeAllTweens();
        this.current = 0;
        this.combatants = {};
        this.card = null;
        this.card_target = null;

        this.stage.removeAllChildren();
        this.initUI();
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