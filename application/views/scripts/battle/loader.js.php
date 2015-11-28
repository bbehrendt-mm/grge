(function() {

    Battle.prototype.load = function() {
        var alias = this;
        this.addResource('field.png', 'grunge.png', 'resist.gif', 'damage.gif', 'kill.gif', 'health.gif', 'arrow_r.gif');

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

            if (v[0] == <?=Model_Combat_Scene::MCS_EV_SWITCH?>)
                alias.queueResource(v[2][1], 'media/icons/' + v[2][1] + '.gif');

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