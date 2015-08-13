(function() {

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
            actor: null,
            container: null,

            messages: [],
            message_processing: false
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
        this.combatants[id].actor.shadow = new createjs.Shadow("rgba(0,0,0,0.5)", 0, 0, 5);

        pos = this.transform(this.combatants[id].pos);
        this.combatants[id].actor.x = -24;
        this.combatants[id].actor.y = -24;
        this.combatants[id].actor.scaleX = this.combatants[id].actor.scaleY = 3;
        this.combatants[id].actor.alpha = 0;

        this.combatants[id].container = new createjs.Container();
        this.combatants[id].container.x = pos.x;
        this.combatants[id].container.y = pos.y;
        this.combatants[id].container.z = pos.y;

        this.combatants[id].container.addChild(this.combatants[id].actor);
        this.stage.addChild(this.combatants[id].container);

        var alias = this;
        createjs.Tween.get(this.combatants[id].actor, {loop: false}).to({alpha: 1, scaleX: 1, scaleY: 1, x: -8, y: -8}, 100).call(function() {
            alias.proceed();
        });
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_NEXT?>] = function(id) {
        var blip = new createjs.Shape();
        blip.x = blip.y = 0;
        blip.scaleX = blip.scaleY = 0;

        this.showActorCard(id);

        this.combatants[id].container.addChildAt(blip, 0);
        blip.graphics
            .setStrokeStyle(3)
            .beginStroke("FFFFFF")
            .beginFill('#AAAAAA')
            .drawCircle(0, 0, 32);

        var alias = this;
        createjs.Tween.get(blip, {loop: false})
            .to({scaleX: 1, scaleY: 1, alpha: 0}, 300)
            .wait(this.idle)
            .call(function() {
                alias.combatants[id].container.removeChildAt(0);
                alias.proceed();
            });
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_MOVE?>] = function(id, pos, distance, target) {
        this.combatants[id].pos = {x: pos[0], y: pos[1], z: pos[1]};
        var alias = this;
        createjs.Tween.get(this.combatants[id].container, {loop: false}).to(this.transform(this.combatants[id].pos), 1000).call(function() {
            alias.proceed();
        });
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_DAMAGE?>] = function(id, damage, kills, death) {
        if (damage === undefined) damage = 0;
        if (kills === undefined) kills = 0;

        this.combatants[id].health = {
            health: this.combatants[id].health.health - (damage - kills * this.combatants[id].health.max),
            max: this.combatants[id].health.max,
            count: this.combatants[id].health.count - kills
        };
        var alias = this;

        //TODO: Move to attack handler
        this.addTargetCard(id);

        if (damage > 0) {
            this.characterPopupMessage(id, '' + Math.round(damage * 10)/10, 'damage.gif');
            if (kills > 0) this.characterPopupMessage(id, '' + kills, 'kill.gif');
        } else this.characterPopupMessage(id, '', 'resist.gif');

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

            body.x = body.y = -8;
            body.alpha = 0;
            this.combatants[id].container.addChild(body);

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({alpha: 0, scaleY: 0.5, y: 0}, 200)
                .call(function() {
                    alias.combatants[id].container.removeChild(alias.combatants[id].actor);
                });
            createjs.Tween.get(body, {loop: false})
                .to({alpha: 1}, 200)
                .call(function() {
                    alias.proceed();
                });


        } else if (damage > 0) {
            var dist = Math.max(Math.min(Math.ceil(damage / 1.5), 6), 0);

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({x: dist - 8, y: dist - 8}, 100)
                .to({x: -dist - 8, y: -dist - 8}, 100)
                .to({x: -8, y: -8}, 100)
                .call(function() {
                    alias.proceed();
                });
        } else this.proceed();

    };

})();