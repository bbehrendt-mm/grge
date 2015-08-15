(function() {

    Battle.prototype.events = {};

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>] = function(id, group, name, avatar, type, pos, strength, stats) {
        this.combatants[id] = {
            group: group,
            name: name,
            avatar: avatar,
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

        var inverse = pos[0] > 32;

        switch (this.combatants[id].type) {
            case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                this.combatants[id].actor = new createjs.Bitmap(this.getResource('player.gif'));
                break;
            case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                this.combatants[id].actor = new createjs.Bitmap(this.getResource('zombie.gif'));
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

        var blackbox = new createjs.Container();
        blackbox.x = pos.x + (inverse ? 10 : -10);
        blackbox.y = pos.y - 10;
        blackbox.scaleX = 0;
        blackbox.z = -99;

        var txt = new createjs.Text(type === <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?> ? (this.combatants[id].health.count + ' x ' + name) : name, 'bold 15px sans-serif', '#ffffff');
        var length = txt.getBounds().width + 40;
        txt.x = 20;
        txt.y = 2;

        var bb_bg = new createjs.Shape();
        bb_bg.graphics
            .setStrokeStyle(1)
            .beginStroke('rgba(0,0,0,0.6)')
            .beginFill('rgba(0,0,0,0.6)')
            .drawRect(0,0,length,20);

        blackbox.addChild(bb_bg);
        blackbox.addChild(txt);
        this.stage.addChild(blackbox);

        var alias = this;
        createjs.Tween.get(this.combatants[id].actor, {loop: false})
            .to({alpha: 1, scaleX: 1, scaleY: 1, x: -8, y: -8}, 100)
            .call(function() {
                createjs.Tween.get(blackbox)
                    .to({scaleX: 1, x: inverse ? (pos.x + (10 - length)) : blackbox.x}, 100)
                    .to({scaleX: 1.1, x: inverse ? (pos.x + (10 - length * 1.1)) : blackbox.x}, 1000)
                    .call(function() {
                        alias.proceed();
                    })
                    .to({scaleX: 0, alpha: 0, x: inverse ? (pos.x + (10 - length * 1.11)) : (length * 1.01 + pos.x)}, 100)
                    .call(function() {
                        alias.stage.removeChild(blackbox);
                    });
            })
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

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_ATTACK?>] = function(id_atk, id_def, ammo, weapon) {
        this.showActorCard(id_atk, this.formatVariantLine([this.getResource(weapon[1]), weapon[0]], "bold 12px Arial", "#ffffff"));
        this.addTargetCard(id_def);
        this.proceed();
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

        var line = null;

        if (damage > 0) {
            var dmg_show = Math.round(damage * 10)/10;
            this.characterPopupMessage(id, '' + dmg_show, 'damage.gif');

            if (kills > 0) {
                this.characterPopupMessage(id, '' + kills, 'kill.gif');
                line = this.formatVariantLine([this.getResource('damage.gif'), dmg_show, null, this.getResource('kill.gif'), kills], "bold 12px Arial", "#ffffff");
            } else line = this.formatVariantLine([this.getResource('damage.gif'), dmg_show], "bold 12px Arial", "#ffffff");
        } else {
            this.characterPopupMessage(id, '', 'resist.gif');
            line = this.formatVariantLine([this.getResource('resist.gif'), <?=__j('Watz!')?>], "bold 12px Arial", "#ffffff");
        }

        this.addTargetCard(id, line);

        if (death) {
            var body;
            switch (this.combatants[id].type) {
                case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                    body = new createjs.Bitmap(alias.getResource('player_dead.gif'));
                    break;
                case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                    body = new createjs.Bitmap(alias.getResource('zombie_dead.gif'));
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
                    alias.proceed(2);
                });


        } else if (damage > 0) {
            var dist = Math.max(Math.min(Math.ceil(damage / 1.5), 6), 0);

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({x: dist - 8, y: dist - 8}, 100)
                .to({x: -dist - 8, y: -dist - 8}, 100)
                .to({x: -8, y: -8}, 100)
                .call(function() {
                    alias.proceed(2);
                });
        } else this.proceed(2);


    };

})();