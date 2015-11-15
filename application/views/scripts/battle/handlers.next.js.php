(function() {
    Battle.prototype.events[<?=Model_Combat_Scene::MCS_EV_NEXT?>] = function(id) {
        var blip = new createjs.Shape();
        blip.x = blip.y = 0;
        blip.scaleX = blip.scaleY = 0;

        this.showActorCard(id);

        this.combatants[id].container.addChildAt(blip, 0);
        blip.graphics
            .setStrokeStyle(3)
            .beginStroke("FFFFFF")
            .beginFill('#AAAAAA')
            .drawCircle(0, 0, 32);

        var alias = this;
        createjs.Tween.get(blip, {loop: false})
            .to({scaleX: 1, scaleY: 1, alpha: 0}, 300)
            .wait(this.idle)
            .call(function() {
                alias.combatants[id].container.removeChildAt(0);
                alias.proceed();
            });
    };
})();