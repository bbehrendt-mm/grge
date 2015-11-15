(function() {
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
            'new': true,

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
                alias.showActorCard(id);
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
})();