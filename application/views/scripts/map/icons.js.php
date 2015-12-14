<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {
    Gamemap.prototype.renderIcons = function() {
        this.iconLayer.removeAllChildren();
        this.icons = {};

        var alias = this;

        var containerSize = 20;

        $.each(this.data.locations, function(id, location) {

            alias.icons[id] = new createjs.Container();
            alias.icons[id].x = alias.icons[id].x_orig = location.x;
            alias.icons[id].y = alias.icons[id].y_orig = location.y;

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

            alias.icons[id].addChild(alias.createCentralizedBitmapContainer('places/' + location.icon));

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

            alias.icons[id].on('click', function() {
                alias.handler(id, 'click');
            });

            alias.icons[id].on('rollover', function() {
                this.highlight = true;

                alias.handler(id, 'mouseover');
                var highlight_color = location.distance > alias.data.radius ? '#FF0000' : '#69E7FF';

                $.each(alias.data.locations[id].nodes, function(i, n) {
                    alias.dots[n].highlight = highlight_color;
                });
                $.each(alias.data.locations[id].route, function(i, n) {
                    alias.icons[n].highlight = highlight_color;
                });
            });

            alias.icons[id].on('rollout', function() {
                this.highlight = false;

                alias.handler(id, 'mouseout');

                $.each(alias.data.locations[id].nodes, function(i, n) {
                    alias.dots[n].highlight = false;
                });
                $.each(alias.data.locations[id].route, function(i, n) {
                    alias.icons[n].highlight = false;
                });
            });
        });
    };
})();