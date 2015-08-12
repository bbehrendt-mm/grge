(function() {

    Battle.prototype.characterPopupMessage = function(id, message, icon) {
        if (message !== undefined)
            this.combatants[id].messages.push([message, icon]);

        if (!this.combatants[id].messages.length)
            this.combatants[id].message_processing = false;
        else if (message === undefined || !this.combatants[id].message_processing) {
            var alias = this;
            this.combatants[id].message_processing = true;

            var entry = this.combatants[id].messages.shift();

            var txtcontainer = new createjs.Container();
            var text = new createjs.Text(entry[0], "bold 15px Arial", "#aa0000");
            text.x = entry[1] ? 20 : 0;
            text.shadow = new createjs.Shadow("#000000", 0, 0, 2);

            txtcontainer.addChild(text);
            if (entry[1]) {
                var image = new createjs.Bitmap(this.getRessource(entry[1]));
                txtcontainer.addChild(image);
            }

            txtcontainer.x = -Math.round(txtcontainer.getBounds().width/2);
            txtcontainer.y = -10;
            txtcontainer.scaleX = txtcontainer.scaleY = 0.75;
            txtcontainer.alpha = 0;

            this.combatants[id].container.addChild(txtcontainer);
            createjs.Tween.get(txtcontainer, {loop: false})
                .to({y: -30, scaleX: 1, scaleY: 1, alpha: 1}, 800)
                .call(function() {
                    alias.characterPopupMessage(id);
                })
                .to({y: -50}, 800)
                .to({y: -55, alpha: 0}, 200)
                .call(function() {
                    alias.combatants[id].container.removeChild(txtcontainer);
                });
        }
    };

})();