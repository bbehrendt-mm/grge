<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {
    Gamemap.prototype.renderRoads = function() {
        var alias = this;

        this.roadLayer.removeAllChildren();
        this.roads = {};
        this.dots = {};

        $.each(this.data.nodes, function(k, node) {
            alias.dots[k] = new createjs.Container();
            alias.dots[k].x = node.x;
            alias.dots[k].y = node.y;

            alias.dots[k].used = false;
            alias.dots[k].highlight = false;

            var s = new createjs.Shape();
            s.setBounds(-3,-3,3,3);
            alias.dots[k].addChild(s);

            alias.dots[k].on('tick', function() {
                this.scaleX = this.scaleY = 1/alias.scale;
                this.alpha = this.used ? 1 : 0.1;

                var hl_color = typeof this.highlight == 'string' ? this.highlight : '#39ACE5';

                s.graphics.c().setStrokeStyle(this.highlight ? 2 : 1).beginStroke(this.highlight ? hl_color : "#000000").drawCircle(0,0,this.highlight ? 4 : 3).beginFill(this.highlight ? hl_color : '#000000').drawCircle(0,0,this.highlight ? 2 : 1);
            });
            alias.roadLayer.addChild(alias.dots[k]);
        });

        $.each(this.data.network, function(base, targets) {

            $.each(targets, function(n, target) {
                var id = (base > target) ? (target + " " + base) : (base + " " + target);

                if (alias.roads[id]) return;

                alias.roads[id] = new createjs.Container();
                alias.roads[id].x = alias.dots[base].x;
                alias.roads[id].y = alias.dots[base].y;

                alias.roads[id].to_x = alias.dots[target].x;
                alias.roads[id].to_y = alias.dots[target].y;
                alias.roads[id].nodes = (base > target) ? [target,base] : [base,target];

                var s = new createjs.Shape();
                alias.roads[id].addChild(s);

                alias.roads[id].on('tick', function() {
                    var hl = (alias.dots[this.nodes[0]].highlight && alias.dots[this.nodes[1]].highlight);
                    var hl_color;
                    if (typeof alias.dots[this.nodes[0]].highlight == 'string') hl_color = alias.dots[this.nodes[0]].highlight;
                    else if (typeof alias.dots[this.nodes[1]].highlight == 'string') hl_color = alias.dots[this.nodes[1]].highlight;
                    else hl_color = '#39ACE5';

                    s.graphics.c().setStrokeStyle((hl ? 3 : 1)/alias.scale).beginStroke(hl ? hl_color : "#000000").moveTo(0,0).lineTo(this.to_x - this.x, this.to_y - this.y);
                    this.alpha = (alias.dots[this.nodes[0]].used && alias.dots[this.nodes[1]].used) ? 1 : 0.1;
                });
                alias.roadLayer.addChild(alias.roads[id]);
            });
        });

        $.each(this.data.locations, function(k, loc) {
            $.each(loc.nodes, function(ki, node) {
                alias.dots[node].used = true;
            });
        });

        var bounds = this.mapLayer.getBounds();
        this.mapLayer.x = -bounds.x;
        this.mapLayer.y = -bounds.y;

        this.scale = Math.min(this.dimensions[0]/(bounds.width - bounds.x), this.dimensions[1]/(bounds.height - bounds.y));
        this.transformLayer.scaleX = this.transformLayer.scaleY = this.scale;

        bounds = this.stage.getBounds();
        this.transformLayer.x = (this.dimensions[0] - bounds.width)/2;
        this.transformLayer.y = (this.dimensions[1] - bounds.height)/2;

        this.defaultView = {x: this.translateLayer.x, y: this.translateLayer.y, scale: this.scale}
    };
})();