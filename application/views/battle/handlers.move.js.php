(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_MOVE?>] = function(id, pos, distance, target) {
        this.checkNewChar(id, true);
        this.combatants[id].pos = {x: pos[0], y: pos[1], z: pos[1]};
        var alias = this;
        createjs.Tween.get(this.combatants[id].container, {loop: false}).to(this.transform(this.combatants[id].pos), 1000).call(function() {
            alias.proceed();
        });
    };
})();