(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_BREAK?>] = function(id, wpn) {
        this.characterPopupMessage(id, '-', wpn[1]);
        this.proceed();
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_INJURY?>] = function(id, inj) {
        this.characterPopupMessage(id, '+', inj[1]);
        this.proceed();
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_DIALOG?>] = function(id, txt) {
        this.characterDialogMessage(id, txt);
        this.proceed();
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_CHARSFX?>] = function(id, tg, name, s) {
        this.addCombatantEffect(id,tg,name,s);
        this.proceed();
    };

    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_CHARPPU?>] = function(id, txt, ico, translate) {
        this.characterPopupMessage(id, txt, ico);
        this.proceed();
    };
})();