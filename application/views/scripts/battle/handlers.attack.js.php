(function() {
    var to = function(a, b, normalize) {
        var r = {x: b.x - a.x, y: b.y - a.y};
        if (!normalize) return r;
        else {
            var l = Math.sqrt(Math.pow(r.x,2) + Math.pow(r.y, 2));
            return {x: r.x/l, y: r.y/l};
        }
    };

    var length = function(vec) {
        return Math.sqrt(Math.pow(vec.x,2) + Math.pow(vec.y,2));
    };

    var animate_slash = function(factor, speed, center, gore, end_callback) {
        var angle = Math.random() * 360;

        var slash_container = new createjs.Container();
        slash_container.x = center.x;
        slash_container.y = center.y;
        slash_container.z = 100;
        slash_container.rotation = angle;

        var slash = new createjs.Shape();
        slash.graphics
            .beginFill('#FFFFFF')
            .drawCircle(2,0,2);

        slash.x = (factor * -12) - 2;
        slash.alpha = 0;
        slash_container.addChild(slash);
        this.stage.addChild(slash_container);

        var alias = this;
        createjs.Tween.get(slash, {loop: false})
            .to({x: (factor * -10) - 2, alpha: 0.5}, 20/speed)
            .to({x: (factor * -8) - 2, scaleX: 4 * factor, scaleY: 1.5, alpha: 1}, 20/speed)
            .to({x: (factor * 8) - 2, scaleX: 1, scaleY: 1, alpha: 0.2}, 20/speed)
            .to({x: (factor * 10) - 2, alpha: 0}, 20/speed)
            .call(function() {
                if (gore)
                    alias.splatter_line(center, {x: 10 * factor * Math.cos(angle * Math.PI/180), y: 10 * factor * Math.sin(angle * Math.PI/180)}, 100 * factor, 2, 7 * factor)
            })
            .wait(40/speed)
            .call(function() {
                alias.stage.removeChild(slash_container);
                if (end_callback())
                    end_callback();
            });
    };

    var animate_shake = function(id, delta, duration) {
        var o = [this.combatants[id].actor.x, this.combatants[id].actor.y];
        var holder = createjs.Tween.get(this.combatants[id].actor, {loop: false});
        for (var i = 1; i < Math.floor(duration/20); i++)
            holder.to({x: o[0] + Math.random() * delta, y: o[1] + Math.random() * delta}, 50)
        holder.to({x: o[0], y: o[1]}, 50)
    };

    var animation_throw = function(from_id, to_id, icon, rotation_factor, speed_factor, callback) {
        var vec = to(this.combatants[from_id].pos, this.combatants[to_id].pos, false);
        var l = length(vec);

        var projectile = this.createCentralizedBitmapContainer(icon);

        var from = this.transform(this.combatants[from_id].pos);
        var end = this.transform(this.combatants[to_id].pos);
        var time = (l * 90)/speed_factor;
        var rotation = 360 * (l/5) * rotation_factor;

        var p_container = new createjs.Container();

        projectile.scaleX = projectile.scaleY = 0.5;
        p_container.addChild(projectile);

        p_container.x = from.x;
        p_container.y = from.y;

        p_container.z = this.combatants[from_id].actor.z + 0.1;

        this.stage.addChild(p_container);

        var alias = this;
        createjs.Tween.get(p_container, {loop: false})
            .to({x: end.x, y: end.y, z: this.combatants[to_id].actor.z + 0.1}, time);
        createjs.Tween.get(projectile, {loop: false})
            .to({rotation: rotation}, time);
        createjs.Tween.get(projectile, {loop: false})
            .to({y: -2 * l, rotation: rotation/2}, time/2, createjs.Ease.cubicOut)
            .to({y: 0, rotation: rotation}, time/2, createjs.Ease.cubicIn)
            .call(function() {
                alias.stage.removeChild(p_container);
                callback();
            });
    };

    var animation_shoot = function(from_id, to_id, icon, scale, speed_factor, callback) {
        var vec = to(this.combatants[from_id].pos, this.combatants[to_id].pos, false);
        var n = to(this.combatants[from_id].pos, this.combatants[to_id].pos, true);
        var l = length(vec);

        var projectile = this.createCentralizedBitmapContainer(icon);

        var from = this.transform(this.combatants[from_id].pos);
        var end = this.transform(this.combatants[to_id].pos);
        var time = (l * 20)/speed_factor;

        projectile.scaleX = projectile.scaleY = scale;
        projectile.rotation = Math.atan2(n.y, n.x) * (180/Math.PI);

        projectile.x = from.x;
        projectile.y = from.y;

        projectile.z = this.combatants[from_id].actor.z + 0.1;

        this.stage.addChild(projectile);

        var alias = this;
        createjs.Tween.get(projectile, {loop: false})
            .to({x: end.x, y: end.y, z: this.combatants[to_id].actor.z + 0.1}, time)
            .call(function() {
                alias.stage.removeChild(projectile);
                callback();
            });
    };

    var animations = {};

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_NONE?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        this.proceed();
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_PUNCH?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var d = to(this.combatants[id_atk].pos, this.combatants[id_def].pos, true);

        var o1 = [this.combatants[id_atk].actor.x, this.combatants[id_atk].actor.y];
        var o2 = [this.combatants[id_def].actor.x, this.combatants[id_def].actor.y];

        var factor = 1;

        var alias = this;
        createjs.Tween.get(this.combatants[id_atk].actor, {loop: false})
            .to({x: o1[0] - d.x * factor, y: o1[1] - d.y * factor}, 250)
            .to({x: o1[0], y: o1[1]}, 100)
            .call(function() {
                createjs.Tween.get(alias.combatants[id_def].actor, {loop: false})
                    .to({x: o2[0] + d.x * factor, y: o2[1] + d.y * factor}, 100)
                    .to({x: o2[0], y: o2[1]}, 350)
            })
            .to({x: o1[0] + d.x * factor, y: o1[1] + d.y * factor}, 100)
            .to({x: o1[0], y: o1[1]}, 250)
            .wait(100)
            .call(function() {
                alias.proceed();
            });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_ZOMBIE_MUNCH?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var d = to(this.combatants[id_atk].pos, this.combatants[id_def].pos, false);

        var o1 = [this.combatants[id_atk].actor.x, this.combatants[id_atk].actor.y];

        var alias = this;
        var splat = damage ? function() {alias.splatter_blob(alias.transform(alias.combatants[id_def].pos), 10, 8);} : function() {};

        createjs.Tween.get(this.combatants[id_atk].actor, {loop: false})
            .to({x: o1[0] + 0.9 * d.x, y: o1[1]  + 0.9 * d.y}, 350)
            .call(function() {
                animate_shake.call(alias, id_def, 1, 500);
            })
            .wait(250).call(splat)
            .wait(250).call(splat)
            .to({x: o1[0], y: o1[1]}, 350)
            .call(function() {
                alias.proceed();
            })
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SLASH?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animate_slash.call(this, 2, 1, this.transform(this.combatants[id_def].pos), damage > 0, function() {
            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SLASH_MULTI?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animate_slash.call(this, 2, 1, this.transform(this.combatants[id_def].pos), damage > 0, function() {
            animate_slash.call(alias, 2, 1, alias.transform(alias.combatants[id_def].pos), damage > 0, function() {
                animate_slash.call(alias, 3, 1, alias.transform(alias.combatants[id_def].pos), damage > 0, function() {
                    alias.proceed();
                });
            });
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_THROW?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animation_throw.call(this, id_atk, id_def, wpn_icon, 1, 1, function() {
            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_BAT?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animation_shoot.call(this, id_atk, id_def, 'ammo/battery.gif', 0.5, 1, function() {
            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_WATER?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animation_shoot.call(this, id_atk, id_def, 'ammo/water.gif', 0.5, 1.1, function() {
            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_BOLT?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animation_shoot.call(this, id_atk, id_def, 'ammo/bolt.gif', 0.8, 1, function() {
            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_AMMO?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animation_shoot.call(this, id_atk, id_def, 'ammo/ammo.gif', 1, 1.5, function() {
            if (damage > 0) {
                var vec = to(alias.combatants[id_atk].pos, alias.combatants[id_def].pos, true);
                alias.splatter_line(alias.transform(alias.combatants[id_def].pos), {x: 32 * vec.x, y: 32 * vec.y}, 50, 4, 15);
            }

            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_ENERGY?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;
        animation_shoot.call(this, id_atk, id_def, 'ammo/energy.gif', 0.4, 2, function() {
            var explosion = alias.getAnimation('animations/plasma.png', 64, 64);

            var container = new createjs.Container();
            var pos = alias.transform(alias.combatants[id_def].pos);
            container.x = pos.x;
            container.y = pos.y;
            container.z = 90;

            explosion.x = explosion.y = -32;
            explosion.framerate = 25;
            explosion.play();
            container.addChild(explosion);
            alias.stage.addChild(container);

            explosion.addEventListener("animationend", function() {
                alias.stage.removeChild(container);
                alias.proceed();
            });
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_SPLINTER?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var alias = this;

        var muzzle = alias.getAnimation('animations/muzzle.png', 64, 64);

        var container = new createjs.Container();
        var pos = alias.transform(alias.combatants[id_atk].pos);
        container.x = pos.x;
        container.y = pos.y;
        container.z = 90;

        muzzle.x = muzzle.y = -32;
        muzzle.framerate = 45;
        muzzle.play();
        container.addChild(muzzle);
        alias.stage.addChild(container);

        muzzle.addEventListener("animationend", function() {
            alias.stage.removeChild(container);
        });


        animation_shoot.call(this, id_atk, id_def, 'ammo/splinter.gif', 0.4, 2, function() {
            alias.proceed();
        });
    };

    animations[<?=Model_Combat_Weapon::MCW_ANIMATION_CHAINSAW?>] = function(id_atk, id_def, scale, wpn_icon, damage) {
        var d = to(this.combatants[id_atk].pos, this.combatants[id_def].pos, true);

        var alias = this;
        var splat = damage ? function() {alias.splatter_blob(alias.transform(alias.combatants[id_def].pos), 24, 12);} : function() {};

        animate_shake.call(this, id_atk, 1, 1000);
        animate_shake.call(this, id_def, 3, 1000);

        var holder = createjs.Tween.get(this.combatants[id_atk].actor, {loop: false});
        for (var i = 0; i < 10; i++)
            holder.call(splat).call(function() {
                if (damage > 0)
                    alias.splatter_line(alias.transform(alias.combatants[id_def].pos), {x: 24 * d.x, y: 24 * d.y}, 50, 3, 30);

                var smoke = alias.getAnimation('animations/smoke.png', 64, 64);

                var container = new createjs.Container();
                var pos = alias.transform(alias.combatants[id_atk].pos);
                container.x = pos.x;
                container.y = pos.y;
                container.z = 90;
                container.alpha = 0.5;
                container.scaleX = container.scaleY = 0.5;

                smoke.x = smoke.y = -32;
                smoke.framerate = 10;
                smoke.play();
                container.addChild(smoke);
                alias.stage.addChild(container);

                createjs.Tween.get(container, {loop: false})
                    .to({x: pos.x - d.x * 70 + (10-Math.random() * 20), y: pos.y - d.y * 70 + (10-Math.random() * 20), scaleX: 1, scaleY: 1, alpha: 0}, 2000);

                smoke.addEventListener("animationend", function() {
                    alias.stage.removeChild(container);
                });
            }).wait(100);
        holder.call(function() {
            alias.proceed();
        });
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_ATTACK?>] = function(id_atk, id_def, ammo, weapon, damage) {
        this.checkNewChar(id_atk, true);
        this.showActorCard(id_atk, this.formatVariantLine([this.getResource(weapon[1]), weapon[0]], "bold 12px Arial", "#ffffff"));
        this.addTargetCard(id_def);

        if (animations[weapon[2]])
            animations[weapon[2]].call(this, id_atk, id_def, Math.max(0,Math.min(1,damage/10)), weapon[1], damage);
        else this.proceed();
    };
})();