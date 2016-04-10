(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_ESCAPE?>] = function(id, chance) {
        this.checkNewChar(id, true);
        this.combatants[id].escaped = true;

        var str;
        if      (chance > 0.75) str = <?=__j('Mühelose Flucht')?>;
        else if (chance > 0.25) str = <?=__j('Kalkulierter Rückzug')?>;
        else if (chance > 0.25) str = <?=__j('Knapp entkommen')?>;
        else                    str = <?=__j('Verzweifelte Flucht')?>;

        this.showActorCard(id, this.formatVariantLine([this.getResource('escape.gif'), str], "bold 12px Arial", "#ffffff"));

        var alias = this;

        var nw_pos = this.combatants[id].pos;
        nw_pos.x += (nw_pos.x > 32) ? 10 : -10;



        var target = this.transform(nw_pos);
        target.alpha = 0;

        createjs.Tween.get(this.combatants[id].container, {loop: false}).to(target, 500);
        setTimeout(function() {alias.proceed();}, 150)
    };
})();