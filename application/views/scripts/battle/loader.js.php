(function() {

    Battle.prototype.load = function() {
        var alias = this;
        this.addResource('field.png', 'grunge.png', 'resist.gif', 'damage.gif', 'kill.gif', 'health.gif', 'arrow_r.gif');

        for (var i = 1; i <= this.splatter_count; i++)
            this.addResource('splatter/splat' + i + '.png')

        $.each(this.data, function(k,v) {
            if (v[0] == <?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>) {
                if (v[5])
                    alias.queueResource(v[5], v[5]);
                else alias.queueResource('mugshot.png', 'media/img/mugshot.png');

                var sprites = v[10];
                if (!sprites[0] || !sprites[1])
                    switch (v[6]) {
                        case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                            if (!sprites[0]) sprites[0] = 'player.gif';
                            if (!sprites[1]) sprites[1] = 'player_dead.gif';
                            break;
                        case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                            if (!sprites[0]) sprites[0] = 'zombie.gif';
                            if (!sprites[1]) sprites[1] = 'zombie_dead.gif';
                            break;
                    }
                alias.addResource('sprites/' + sprites[0], 'sprites/' + sprites[1]);
            }

            if (v[0] == <?=Model_Combat_Scene::MCS_EV_BREAK?> || v[0] == <?=Model_Combat_Scene::MCS_EV_INJURY?> || v[0] == <?=Model_Combat_Scene::MCS_EV_SWITCH?>)
                alias.queueResource(v[2][1], 'media/icons/' + v[2][1] + '.gif');

            if (v[0] == <?=Model_Combat_Scene::MCS_EV_ATTACK?>) {
                alias.queueResource(v[4][1], 'media/icons/' + v[4][1] + '.gif');
                $.each(v[3], function(k, icn) {
                    if (typeof icn == "object")
                        icn = icn[0];

                    switch (icn) {
                        case '::energy':
                            alias.queueResource(icn, 'media/icons/status_energy.gif');
                            break;
                        default:
                            alias.queueResource(icn, 'media/icons/' + icn + '.gif');
                            break;
                    }

                });
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