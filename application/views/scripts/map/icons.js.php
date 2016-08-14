<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {
    Gamemap.prototype.hover = function(id, trigger_events) {
        this.icons[id].highlight = true;

        if (trigger_events) this.handler(id, 'mouseover');
        var highlight_color = this.data.locations[id].energy > this.data.radius ? '#FF0000' : '#69E7FF';

        var alias = this;
        $.each(this.data.locations[id].nodes, function(i, n) {
            alias.dots[n].highlight = highlight_color;
        });
        $.each(this.data.locations[id].route, function(i, n) {
            alias.icons[n].highlight = highlight_color;
        });
    };

    Gamemap.prototype.unhover = function(id, trigger_events) {
        this.icons[id].highlight = false;

        if (trigger_events) this.handler(id, 'mouseout');

        var alias = this;
        $.each(this.data.locations[id].nodes, function(i, n) {
            alias.dots[n].highlight = false;
        });
        $.each(this.data.locations[id].route, function(i, n) {
            alias.icons[n].highlight = false;
        });
    };

    Gamemap.prototype.toggleTagMode = function() {
        var tg = (this.tagmode = !this.tagmode);
        var big = game.touch();

        $.each(this.icons, function(id, icon) {
            createjs.Tween.get(icon.img_links.icon, {loop: false})
                .to(tg ? {alpha: 0.25} : {alpha: 1}, 200);
            if (icon.img_links.tag)
                createjs.Tween.get(icon.img_links.tag, {loop: false})
                    .to(tg ? {x: 0, y: 0, scaleX: 1, scaleY: 1} : {x: big ? 12 : 8, y: big ? 12 : 8, scaleX: big ? 1 : 0.75, scaleY: big ? 1 : 0.75}, 200);
        });
    };

    Gamemap.prototype.addIconTag = function(id, tag, big_icons) {
        if (this.icons[id].img_links.tag) {
            this.icons[id].removeChild(this.icons[id].img_links.tag);
            this.icons[id].img_links.tag = null;
        }

        if (tag > 0) {
            this.icons[id].img_links.tag = this.createCentralizedBitmapContainer('places/tags/tag_' + tag + '.gif');
            this.icons[id].img_links.tag.scaleX = this.icons[id].img_links.tag.scaleY = big_icons ? 1 : 0.75;
            this.icons[id].img_links.tag.x = this.icons[id].img_links.tag.y = big_icons ? 12 : 8;
            this.icons[id].addChild(this.icons[id].img_links.tag);
        }
    };

    Gamemap.prototype.renderIcons = function() {
        this.iconLayer.removeAllChildren();
        this.icons = {};

        var alias = this;

        var use_big_icons = game.touch();
        var containerSize = use_big_icons ? 36 : 20;

        $.each(this.data.locations, function(id, location) {

            alias.icons[id] = new createjs.Container();
            alias.icons[id].x = alias.icons[id].x_orig = location.x;
            alias.icons[id].y = alias.icons[id].y_orig = location.y;
            alias.icons[id].alpha = game.s.quality() >= 3 ? 0 : 1;

            alias.icons[id].highlight = false;
            alias.icons[id].current_location = (id == alias.data.current);

            var color_border = "#FFFFFF";
            var color_inner = "#FFFFFF";

            alias.icons[id]['classes'] = $.objToArray(location['classes'], true);

            if ($.inArray('open', alias.icons[id]['classes']) >= 0) {color_border = "#975847"; color_inner = "#FF9F68"}
            if ($.inArray('closed', alias.icons[id]['classes']) >= 0) {color_border = "#80493A"; color_inner = "#E08761"}
            if ($.inArray('viewpoint', alias.icons[id]['classes']) >= 0) {color_border = "#cdff92"; color_inner = "#97c669"}
            if ($.inArray('doorway', alias.icons[id]['classes']) >= 0) {color_border = "#bdc66b"; color_inner = "#f2ff9a"}
            if ($.inArray('hideout', alias.icons[id]['classes']) >= 0) {color_border = "#d8a6dc"; color_inner = "#fad5ff"}

            var s = new createjs.Shape();
            s.setBounds(-containerSize/2,-containerSize/2,containerSize/2,containerSize/2);
            alias.icons[id].addChild(s);

            alias.icons[id].img_links = {icon: alias.createCentralizedBitmapContainer('places/' + location.icon), tag: null};
            alias.icons[id].addChild(alias.icons[id].img_links.icon);
            if (location.note.tag > 0 && location.note.tag <= <?=Model_Map_Abstract::MMA_NUMBER_OF_TAGS?>)
                alias.addIconTag(id, location.note.tag, use_big_icons);


            alias.icons[id].on('tick', function() {
                this.scaleX = this.scaleY = 1/alias.scale;

                var hl = this.highlight || this.current_location;
                var hl_color = typeof this.highlight == 'string' ? this.highlight : '#69E7FF';

                s.graphics.c().setStrokeStyle(1).beginStroke(hl ? hl_color : color_border).beginFill(hl ? hl_color : color_inner).drawRoundRectComplex (-containerSize/2,-containerSize/2,containerSize,containerSize, 3, 3, 3, 3);

                if (alias.isZooming) return;

                var globalPos = alias.iconLayer.localToLocal(this.x_orig, this.y_orig, alias.translateLayer);
                globalPos = {x: Math.round(globalPos.x/containerSize)*containerSize, y: Math.round(globalPos.y/containerSize)*containerSize};
                var globalPos_orig = globalPos;

                var dist = 0, xc = 0, yc = 0;
                while (alias.grid_cache[globalPos.x + '_' + globalPos.y] && dist < 5) {

                    if (xc == dist && yc == dist)
                        xc = yc = -(++dist);
                    else if (Math.abs(yc) == dist && xc < dist)
                        xc++;
                    else if (Math.abs(yc) != dist && xc < dist)
                        xc = dist;
                    else if (xc == dist) {
                        yc++; xc = 0;
                    }

                    globalPos = {x: globalPos_orig.x + xc*containerSize, y: globalPos_orig.y + yc*containerSize}
                }

                alias.grid_cache[globalPos.x + '_' + globalPos.y] = true;

                var localPos = alias.translateLayer.localToLocal(globalPos.x, globalPos.y, alias.iconLayer);

                this.x = localPos.x; this.y = localPos.y;

            });
            alias.iconLayer.addChild(alias.icons[id]);
            alias.icons[id].on('contextmenu', function() {return false;});
            alias.icons[id].on('click', function(e) {
                switch (e.nativeEvent.button) {
                    case 0: alias.handler(id, 'click'); break;
                    case 1: case 2: alias.handler(id, 'altclick'); break;
                    default: alias.handler(id, 'anyclick'); break;
                }
                e.preventDefault();
                return false;
            });

            alias.icons[id].on('rollover', function() {
                alias.hover(id, true);
            });

            alias.icons[id].on('rollout', function() {
                alias.unhover(id, true);
            });

            if (game.s.quality() >= 3)
                createjs.Tween.get(alias.icons[id], {loop: false})
                    .to({alpha: 0}, 50 + Math.random() * 500)
                    .to({alpha: 1}, 200)

        });
    };
})();