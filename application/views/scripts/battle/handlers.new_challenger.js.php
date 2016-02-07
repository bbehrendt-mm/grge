(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>] = function(id, group, name, unique, avatar, type, pos, strength, stats, sprites) {
        if (!sprites) sprites = [];
        if (!sprites[0] || !sprites[1])
            switch (type) {
                case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                    if (!sprites[0]) sprites[0] = 'player.gif';
                    if (!sprites[1]) sprites[1] = 'player_dead.gif';
                    break;
                case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                    if (!sprites[0]) sprites[0] = 'zombie.gif';
                    if (!sprites[1]) sprites[1] = 'zombie_dead.gif';
                    break;
            }

        this.combatants[id] = {
            group: group,
            name: name,
            unique: unique,
            avatar: avatar ? avatar : 'mugshot.png',
            type: type,
            sprites: sprites,
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

        this.combatants[id].actor = new createjs.Bitmap(this.getResource('sprites/' + sprites[0]));
        this.combatants[id].actor.shadow = new createjs.Shadow("rgba(0,0,0,0.5)", 0, 0, 5);

        var sph = this.combatants[id].actor.getBounds().height;
        var sphb = sph + 4;
        var spw = this.combatants[id].actor.getBounds().width;
        var spwb = spw + 4;

        pos = this.transform(this.combatants[id].pos);
        this.combatants[id].actor.x = spw * -1.5;
        this.combatants[id].actor.y = sph * -1.5;
        this.combatants[id].actor.scaleX = this.combatants[id].actor.scaleY = 3;
        this.combatants[id].actor.alpha = 0;

        this.combatants[id].container = new createjs.Container();
        this.combatants[id].container.x = pos.x;
        this.combatants[id].container.y = pos.y;
        this.combatants[id].container.z = pos.y;

        this.combatants[id].container.addChild(this.combatants[id].actor);
        this.stage.addChild(this.combatants[id].container);

        var blackbox = new createjs.Container();
        blackbox.x = pos.x + (inverse ? 1 : -1) * spwb/2;
        blackbox.y = pos.y - sphb/2;
        blackbox.scaleX = 0;
        blackbox.z = -99;

        var txt = new createjs.Text(type === <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?> ? (this.combatants[id].health.count + ' x ' + name) : name, 'bold ' + Math.min(22,sph-2) + 'px sans-serif', '#ffffff');
        var length = txt.getBounds().width + spwb * 2;
        var height = txt.getBounds().height;
        txt.x = spwb;
        txt.y = (sphb - height)/2;

        var bb_bg = new createjs.Shape();
        bb_bg.graphics
            .setStrokeStyle(1)
            .beginStroke('rgba(0,0,0,0.6)')
            .beginFill('rgba(0,0,0,0.6)')
            .drawRect(0,0,length, sphb);

        blackbox.addChild(bb_bg);
        blackbox.addChild(txt);
        this.stage.addChild(blackbox);

        var alias = this;
        createjs.Tween.get(this.combatants[id].actor, {loop: false})
            .to({alpha: 1, scaleX: 1, scaleY: 1, x: spw * -0.5, y: sph * -0.5}, 100)
            .call(function() {
                alias.showActorCard(id);
                createjs.Tween.get(blackbox)
                    .to({scaleX: 1, x: inverse ? (pos.x + (spwb/2 - length)) : blackbox.x}, 100)
                    .to({scaleX: 1.1, x: inverse ? (pos.x + (spwb/2 - length * 1.1)) : blackbox.x}, 1000)
                    .call(function() {
                        alias.proceed();
                    })
                    .to({scaleX: 0, alpha: 0, x: inverse ? (pos.x + (spwb/2 - length * 1.11)) : (length * 1.01 + pos.x)}, 100)
                    .call(function() {
                        alias.stage.removeChild(blackbox);
                    });
            })
    };
})();