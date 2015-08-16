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

    Battle.prototype.load = function() {
        var alias = this;
        this.addResource('field.png', 'resist.gif', 'damage.gif', 'kill.gif', 'health.gif');

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


            if (v[0] == <?=Model_Combat_Scene::MCS_EV_ATTACK?>)
                alias.queueResource(v[4][1], 'media/icons/' + v[4][1] + '.gif');

        });
    };

})();