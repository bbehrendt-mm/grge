(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_BREAK?>] = function(id, wpn) {
        this.characterPopupMessage(id, '-', wpn[1]);
        this.proceed();
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_INJURY?>] = function(id, inj) {
        this.characterPopupMessage(id, '+', inj[1]);
        this.proceed();
    };
})();