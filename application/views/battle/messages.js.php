(function() {

    var getCardContents = function(c, line, animate) {

        var avatar_bmp = new createjs.Bitmap(this.getResource(c.avatar));
        var dim = Math.max(this.getResource(c.avatar).height, this.getResource(c.avatar).width);
        var ox = this.getResource(c.avatar).width == dim ? 0 : Math.round((dim - this.getResource(c.avatar).width)/2);
        var oy = this.getResource(c.avatar).height == dim ? 0 : Math.round((dim - this.getResource(c.avatar).height)/2);

        avatar_bmp.cache(-ox, -oy, dim, dim, 64/dim);

        var avatar = new createjs.Shape();
        avatar.graphics
            .setStrokeStyle(2)
            .beginStroke("#999999")
            .beginBitmapFill(avatar_bmp.cacheCanvas)
            .drawCircle(32,32,24);

        var nametxt = new createjs.Text((c.type == <?=Model_Combat_Actor::MCA_TYPE_ZOMBIE?> && c.health.count > 0 ? (c.health.count + ' x ') : '') + c.name, "bold 15px Arial", "#ffffff");
        nametxt.x = 64;
        nametxt.y = 8;

        var health_icon = new createjs.Bitmap(this.getResource(c.health.count > 0 ? 'health.gif' : 'kill.gif'));
        health_icon.x = 64;
        health_icon.y = 24;

        var healthtxt = new createjs.Text(c.health.count > 0 ? Math.round(c.health.health*10)/10 + ' / ' + c.health.max : <?=__j('Vernichtet!')?>, "bold 12px Arial", c.health.count > 0 ? "#FF1D03" : 'white');
        healthtxt.x = 88 + 47 - healthtxt.getBounds().width/2;
        healthtxt.y = c.health.count > 0 ? 24 : 28;

        var healthbar = new createjs.Container();
        healthbar.x = 88;
        healthbar.y = 36;

        if (c.health.count > 0) {
            var hb_bg = new createjs.Shape();
            hb_bg.graphics
                .beginFill('#900431')
                .rect(0, 0, 94, 4);

            var hb_br = new createjs.Shape();
            hb_br.graphics
                .beginFill('#FF1D03')
                .rect(0, 0, Math.round(94 * (c.health.health / c.health.max)), 4);

            healthbar.addChild(hb_bg);
            healthbar.addChild(hb_br);
        }

        var line_container = new createjs.Container();
        line_container.x = 64;
        line_container.y = 46;

        if (!animate) {
            line_container.alpha = 0;
            createjs.Tween.get(line_container, {loop: false})
                .to({alpha: 1}, 150);
        }


        if (line)
            line_container.addChild(line);

        return [avatar, nametxt, health_icon, healthtxt, healthbar, line_container];
    };

    Battle.prototype.formatVariantLine = function(data, textFormat, textColor) {
        var line = new createjs.Container();
        var offset = 0;

        $.each(data, function(k, v) {
            if (v === 0) offset += 10;
            else {
                var tmp = (typeof(v) == 'object') ? new createjs.Bitmap(v) : new createjs.Text(v, textFormat, textColor);
                tmp.x = offset;
                tmp.y = (typeof(v) == 'object') ? 0 : Math.round((16 - tmp.getBounds().height)/2);
                line.addChild(tmp);
                offset += 4 + ((typeof(v) == 'object') ? 16 : tmp.getBounds().width);
            }
        });

        return line;
    };

    Battle.prototype.showActorCard = function(id, line) {
        var animate;
        if (id !== false) {
            animate = !this.card || this.card.base_id != id;
            if (animate) this.card_target = null;
        }

        var alias_card = this.card;
        var alias = this;
        if ((animate || id === false) && alias_card)
            createjs.Tween.get(alias_card, {loop: false})
                .to({y: alias_card.y <= 10 ? -34 : 370, alpha: 0}, 200)
                .call(function() {
                    alias.stage.removeChild(alias_card);
                });

        if (id === false) return;

        var invert = this.combatants[id].pos.y > 30;

        if (animate) {
            this.card = new createjs.Container();
            this.card.x = this.card.alpha = 0;
            this.card.y = invert ? -34 : 370;
            this.card.base_id = id;
        } else this.card.removeAllChildren();

        var background = new createjs.Shape();
        background.graphics
            .beginFill('rgba(0,0,0,0.6)')
            .rect(0,0,640,64);

        var content = getCardContents.call(this, this.combatants[id], line, animate);

        this.card.addChild(background);

        $.each(content, function(k,v) {alias.card.addChild(v);});

        if (animate) {
            this.stage.addChild(this.card);
            createjs.Tween.get(this.card, {loop: false})
                .to({y: invert ? 0 : 336, alpha: 1}, 200);
        }
    };

    Battle.prototype.addTargetCard = function(id, line) {
        if (!this.card)
            return;

        var animate = !this.card_target || this.card_target.base_id != id;

        console.log('Adding target card, animation state is', animate);

        var alias_card = this.card_target;
        var alias = this;
        if (animate && alias_card)
            createjs.Tween.get(alias_card, {loop: false})
                .to({x: 368, alpha: 0}, 200)
                .call(function() {
                    alias.card.removeChild(alias_card);
                });

        if (animate) {
            this.card_target = new createjs.Container();
            this.card_target.alpha = 0;
            this.card_target.x = 368;
            this.card_target.y = 0;
            this.card_target.base_id = id;
        } else this.card_target.removeAllChildren();


        var background = new createjs.Shape();
        background.graphics
            .beginFill('rgba(0,0,0,0.3)')
            .moveTo(-32, 0).lineTo(306,0).lineTo(306, 64).lineTo(-32,64).lineTo(-16,32).lineTo(-32, 0);

        var content = getCardContents.call(this, this.combatants[id], line, animate);

        this.card_target.addChild(background);
        $.each(content, function(k,v) {alias.card_target.addChild(v);});

        if (animate) {
            this.card.addChild(this.card_target);

            createjs.Tween.get(this.card_target, {loop: false})
                .to({x: 334, alpha: 1}, 200);
        }

    };

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
                var image = new createjs.Bitmap(this.getResource(entry[1]));
                txtcontainer.addChild(image);
            }

            var w = Math.round(txtcontainer.getBounds().width/2);
            txtcontainer.x = -w * 0.75;
            txtcontainer.y = -7.5;
            txtcontainer.scaleX = txtcontainer.scaleY = 0.75;
            txtcontainer.alpha = 0;

            this.combatants[id].container.addChild(txtcontainer);
            createjs.Tween.get(txtcontainer, {loop: false})
                .to({x: -w, y: -30, scaleX: 1, scaleY: 1, alpha: 1}, 800)
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