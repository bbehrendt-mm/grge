(function() {

    var ef_drunk = function(id,strength) {

        var alias = this;

        var place_bubble = function() {

            if (!alias.combatants[id].sfx.drunk) return;

            var size = Math.random() * 3;
            var bubble = new createjs.Shape();

            bubble.graphics
                .setStrokeStyle(1)
                .beginStroke("#e5be8b")
                .beginFill("#ffe1c5")
                .drawCircle(0,0,size);

            bubble.x = alias.combatants[id].container.x + 4 * Math.random() - 2;
            bubble.y = alias.combatants[id].container.y;
            bubble.z = alias.combatants[id].container.z - 0.1;
            bubble.alpha = 0.1 + Math.random() * 0.3;

            alias.stage.addChild(bubble);

            alias.createTween(bubble, {loop: false})
                .to({x: alias.combatants[id].container.x + (40 * Math.random() - 20), y: alias.combatants[id].container.y - 30 - 10 * Math.random()}, 2000 + 500 * Math.random())
                .call(function() {
                    alias.stage.removeChild(bubble);
                    place_bubble();
                })

        };

        var num = 2 * 13 * strength;
        var t = this.createTween(this.stage, {loop: false})
        for (var i = 0; i < num; i++)
            t.call(function() {place_bubble(alias.combatants[id].container.x,alias.combatants[id].container.y,alias.combatants[id].container.z)}).wait(2250/num);

    }

    Battle.prototype.addCombatantEffect = function(id,toggle,name,strength) {
        this.combatants[id].sfx[name] = toggle && strength > 0;
        if (this.combatants[id].sfx[name])
            switch (name) {

                case 'drunk':
                   ef_drunk.call(this,id,strength);
                   break;
                default: console.error("Unknown C-SFX: " + name);

            }
    };



})();