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
        background.graphics.beginBitmapFill(this.getRessource('field.png')).drawRect(0,0,640,400);
        background.z = -100;
        this.stage.addChild(background);
    };

    Battle.prototype.addRessource = function(name) {
        if (this.ressources[name]) return;

        this.loadstate++;
        var alias = this;

        this.ressources[name] = new Image();
        this.ressources[name].onload = function() {
            alias.loadstate--;
            if (!alias.loadstate) {
                alias.initUI();
                if (alias.waiting)
                    alias.proceed();
            }
        };

        this.ressources[name].src = '../media/icons/battle/' + name;
    };

    Battle.prototype.getRessource = function(name) {
        return this.ressources[name];
    };

    Battle.prototype.load = function() {
        var alias = this;
        this.addRessource('field.png');

        $.each(this.data, function(k,v) {
            if (v[0] == <?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>)
                switch (v[4]) {
                    case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                        alias.addRessource('player.gif');
                        alias.addRessource('player_dead.gif');
                        break;
                    case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                        alias.addRessource('zombie.gif');
                        alias.addRessource('zombie_dead.gif');
                        break;
                }
        });
    };

    Battle.prototype.transform = function(pos) {
        return {x: Math.round(pos.x * 9) + 32,y: Math.round(pos.y * 9) + 20};
    };

    Battle.prototype.performZSorting = function() {
        this.stage.sortChildren(function(a, b) {
            return a.z - b.z;
        })
    };

    Battle.prototype.events = {};

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>] = function(id, group, name, type, pos, strength, stats) {
        this.combatants[id] = {
            group: group,
            name: name,
            type: type,
            pos: {x: pos[0], y: pos[1]},
            health: {
                health: strength[0],
                max: strength[1],
                count: strength[2]
            },
            stats: {
                ini: stats[0],
                dmg: stats[1],
                res: stats[2],
                acc: stats[3]
            },
            actor: null
        };

        switch (this.combatants[id].type) {
            case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                this.combatants[id].actor = new createjs.Bitmap(this.getRessource('player.gif'));
                break;
            case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                this.combatants[id].actor = new createjs.Bitmap(this.getRessource('zombie.gif'));
                break;
            default:
                console.error('Unknown actor type ' + this.combatants[id].type);
        }

        pos = this.transform(this.combatants[id].pos);
        this.combatants[id].actor.x = pos.x;
        this.combatants[id].actor.y = pos.y;
        this.combatants[id].actor.z = pos.y;

        this.stage.addChild(this.combatants[id].actor);
        this.proceed();
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_NEXT?>] = function(id) {
        var blip = new createjs.Shape();
        blip.x = this.combatants[id].actor.x + 8;
        blip.y = this.combatants[id].actor.y + 8;
        blip.z = this.combatants[id].actor.z - 0.1;

        blip.scaleX = blip.scaleY = 0;
        this.stage.addChild(blip);
        blip.graphics
            .setStrokeStyle(3)
            .beginStroke("FFFFFF")
            .beginFill('#AAAAAA')
            .drawCircle(0, 0, 32);

        var alias = this;
        createjs.Tween.get(blip, {loop: false}).to({scaleX: 1, scaleY: 1, alpha: 0}, 300).call(function() {
            alias.stage.removeChild(blip);
            alias.proceed();
        });
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_MOVE?>] = function(id, pos, distance, target) {
        this.combatants[id].pos = {x: pos[0], y: pos[1], z: pos[1]};
        var alias = this;
        createjs.Tween.get(this.combatants[id].actor, {loop: false}).to(this.transform(this.combatants[id].pos), 1000).call(function() {
            alias.proceed();
        });
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_DAMAGE?>] = function(id, damage, kills, death) {
        this.combatants[id].health = {
            health: this.combatants[id].health - (damage - kills * this.combatants[id].max),
            max: this.combatants[id].max,
            count: this.combatants[id].count - kills
        };
        var alias = this;

        if (death) {

            var body;
            switch (this.combatants[id].type) {
                case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                    body = new createjs.Bitmap(alias.getRessource('player_dead.gif'));
                    break;
                case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                    body = new createjs.Bitmap(alias.getRessource('zombie_dead.gif'));
                    break;
                default:
                    console.error('Unknown actor type ' + this.combatants[id].type);
            }

            body.x = this.combatants[id].actor.x;
            body.y = this.combatants[id].actor.y;
            body.z = this.combatants[id].actor.z;
            body.alpha = 0;
            this.stage.addChild(body);

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({alpha: 0, scaleY: 0.5, y: this.combatants[id].actor.y + 8}, 200)
                .call(function() {
                    alias.stage.removeChild(alias.combatants[id].actor);
                });
            createjs.Tween.get(body, {loop: false})
                .to({alpha: 1}, 200)
                .call(function() {
                    alias.proceed();
                });


        } else if (damage > 0) {
            var current = {x: this.combatants[id].actor.x, y: this.combatants[id].actor.y};
            var dist = Math.max(Math.min(Math.ceil(damage / 1.5), 6), 0);
            var shake = {x: current.x + dist, y: current.y + dist};

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({x: current.x + dist, y: current.y + dist}, 100)
                .to({x: current.x - dist, y: current.y - dist}, 100)
                .to(current, 100)
                .call(function() {
                    alias.proceed();
                });
        } else this.proceed();

    };

    Battle.prototype.proceed = function(autoplay) {
        if (autoplay)
            this.idle = autoplay;

        if (this.loadstate) {
            this.waiting = true;
            return;
        }

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
            }, this.idle);
        else alias.proceed();
    }
})();