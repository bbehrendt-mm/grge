(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_DAMAGE?>] = function(id, damage, kills, death) {
        if (damage === undefined) damage = 0;
        if (kills === undefined) kills = 0;

        this.combatants[id].health = {
            health: this.combatants[id].health.health - (damage - kills * this.combatants[id].health.max),
            max: this.combatants[id].health.max,
            count: this.combatants[id].health.count - kills
        };
        var alias = this;

        var line = null;

        if (damage > 0) {
            var dmg_show = Math.round(damage * 10)/10;
            this.characterPopupMessage(id, '' + dmg_show, 'damage.gif');

            if (kills > 0) {
                this.characterPopupMessage(id, '' + kills, 'kill.gif');
                line = this.formatVariantLine([this.getResource('damage.gif'), dmg_show, null, this.getResource('kill.gif'), kills], "bold 12px Arial", "#ffffff");
            } else line = this.formatVariantLine([this.getResource('damage.gif'), dmg_show], "bold 12px Arial", "#ffffff");

            this.splatter_blob(this.transform(this.combatants[id].pos), 3 + Math.min(32, Math.round(damage/2)), 8 + Math.min(16, Math.round(damage/4)))
        } else {
            this.characterPopupMessage(id, '', 'resist.gif');
            line = this.formatVariantLine([this.getResource('resist.gif'), <?=__j('Watz!')?>], "bold 12px Arial", "#ffffff");
        }

        this.addTargetCard(id, line);

        if (death) {
            var body = new createjs.Bitmap(alias.getResource('sprites/' + this.combatants[id].sprites[1]));

            body.x = body.y = -8;
            body.alpha = 0;
            this.combatants[id].container.addChild(body);

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({alpha: 0, scaleY: 0.5, y: 0}, 200)
                .call(function() {
                    alias.combatants[id].container.removeChild(alias.combatants[id].actor);
                });
            createjs.Tween.get(body, {loop: false})
                .to({alpha: 1}, 200)
                .call(function() {
                    alias.proceed(2);
                });


        } else if (damage > 0) {
            var dist = Math.max(Math.min(Math.ceil(damage / 1.5), 6), 0);

            createjs.Tween.get(this.combatants[id].actor, {loop: false})
                .to({x: dist - 8, y: dist - 8}, 100)
                .to({x: -dist - 8, y: -dist - 8}, 100)
                .to({x: -8, y: -8}, 100)
                .call(function() {
                    alias.proceed(2);
                });
        } else this.proceed(2);


    };

})();