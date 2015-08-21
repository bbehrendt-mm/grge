(function() {

    Battle.prototype.queueResource = function(name, path) {
        if (this.ressources[name]) return;

        this.loadstate++;

        this.ressources[name] = new Image();

        var alias = this;
        this.ressources[name].onload = function() {
            alias.loadstate--;
            if (!alias.loadstate) {
                alias.initUI();
                if (alias.waiting)
                    alias.begin();
            }
        };

        this.ressources[name].src = /^(\w*?):\/\//.test(path) ? path : ('../' + path);
    };

    Battle.prototype.addResource = function() {
        for (var i = 0; i < arguments.length; i++) {
            var name = arguments[i];
            this.queueResource(name, 'media/icons/battle/' + name);
        }
    };

    Battle.prototype.getResource = function(name) {
        return this.ressources[name];
    };

    Battle.prototype.getAnimation = function(name, sizeX, sizeY) {
        var spriteSheet = new createjs.SpriteSheet({
            images: [this.getResource(name)],
            frames: {width: sizeX, height: sizeY,  count:32, regX: 0, regY:0, spacing:0, margin:0}
        });

        return new createjs.Sprite(spriteSheet);
    };

    Battle.prototype.createCentralizedBitmapContainer = function(path) {
        var bmp = this.getResource(path);

        var bitmap = new createjs.Bitmap(bmp);
        bitmap.x = -Math.round(bmp.width/2);
        bitmap.y = -Math.round(bmp.height/2);

        var container = new createjs.Container();
        container.addChild(bitmap);
        container.cache(bitmap.x, bitmap.y, bmp.width, bmp.height, 1);

        return container;
    };

    Battle.prototype.load = function() {
        var alias = this;
        this.addResource('field.png', 'grunge.png', 'resist.gif', 'damage.gif', 'kill.gif', 'health.gif');

        for (var i = 1; i <= this.splatter_count; i++)
            this.addResource('splatter/splat' + i + '.png')

        $.each(this.data, function(k,v) {
            if (v[0] == <?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>) {
                if (v[4])
                    alias.queueResource(v[4], v[4]);

                switch (v[5]) {
                    case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                        alias.addResource('player.gif', 'player_dead.gif');
                        break;
                    case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                        alias.addResource('zombie.gif', 'zombie_dead.gif');
                        break;
                }
            }


            if (v[0] == <?=Model_Combat_Scene::MCS_EV_ATTACK?>) {
                alias.queueResource(v[4][1], 'media/icons/' + v[4][1] + '.gif');
                switch(v[4][2]) {
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_BAT?>:
                        alias.addResource('ammo/battery.gif');
                        break;
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_AMMO?>:
                        alias.addResource('ammo/ammo.gif');
                        break;
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_WATER?>:
                        alias.addResource('ammo/water.gif');
                        break;
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_ENERGY?>:
                        alias.addResource('ammo/energy.gif', 'animations/plasma.png');
                        break;
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_SPLINTER?>:
                        alias.addResource('ammo/splinter.gif', 'animations/muzzle.png');
                        break;
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_SHOT_BOLT?>:
                        alias.addResource('ammo/bolt.gif');
                        break;
                    case <?=Model_Combat_Weapon::MCW_ANIMATION_CHAINSAW?>:
                        alias.addResource('animations/smoke.png');
                        break;
                }
            }


        });
    };

})();