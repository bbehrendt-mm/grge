(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_SWITCH?>] = function(id, weapon) {
        this.showActorCard(id, this.formatVariantLine([this.getResource('arrow_r.gif'), this.checkNewChar(id) ? <?=__j('Ausrüsten: ')?> : <?=__j('Waffenwechsel: ')?>,this.getResource(weapon[1]), weapon[0]], "bold 12px Arial", "#ffffff"));
        this.checkNewChar(id, true);
        var reverse = this.combatants[id].pos.x > 32;

        var img = this.createCentralizedBitmapContainer(weapon[1]);

        img.alpha = 0;
        img.scaleX = reverse ? 1 : -1;
        img.x = (img.y = 8) * (reverse ? -1 : 1);

        this.combatants[id].container.addChild(img);

        var alias = this;
        createjs.Tween.get(img, {loop: false})
            .to({y: 0, alpha: 1}, 300)
            .wait(this.idle)
            .to({y: -8, alpha: 0}, 300)
            .call(function() {
                alias.combatants[id].container.removeChild(img);
                alias.proceed(0);
            })
    };
})();