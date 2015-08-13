(function() {

    Battle.prototype.addRessource = function() {
        var alias = this;
        for (var i = 0; i < arguments.length; i++) {
            var name = arguments[i];

            if (this.ressources[name]) return;

            this.loadstate++;

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
        }

    };

    Battle.prototype.getRessource = function(name) {
        return this.ressources[name];
    };

    Battle.prototype.load = function() {
        var alias = this;
        this.addRessource('field.png', 'resist.gif', 'damage.gif', 'kill.gif', 'health.gif');

        $.each(this.data, function(k,v) {
            if (v[0] == <?=Model_Combat_Scene::MCS_EV_NEW_CHALLENGER?>)
                switch (v[4]) {
                    case <?=Model_Combat_Actor::MCA_TYPE_PLAYER?>:
                        alias.addRessource('player.gif', 'player_dead.gif');
                        break;
                    case <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?>:
                        alias.addRessource('zombie.gif', 'zombie_dead.gif');
                        break;
                }
        });
    };

})();