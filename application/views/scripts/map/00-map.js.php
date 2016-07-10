<?php
/**
 * @var array $version_data Version Data
 */
?>

(function() {
    Gamemap = function(canvas, data, width, heigth) {
        var alias = this;
        CanvasAnimationModule.call(this, canvas, 60, 'media/icons/',
            function() {alias.grid_cache = {};},
            function() {alias.render();}
        );

        this.stage.enableMouseOver(10);

        this.data = data;
        this.dimensions = [width === undefined ? 800 : width, heigth === undefined ? 600 : heigth];
        this.transform = [0,0];

        this.scale = 1;
        this.tagmode = false;

        this.title = null;
        this.mapLayer = new createjs.Container();
        this.roadLayer = new createjs.Container();
        this.iconLayer = new createjs.Container();
        this.transformLayer = new createjs.Container();
        this.mapLayer.addChild(this.roadLayer);
        this.mapLayer.addChild(this.iconLayer);
        this.transformLayer.addChild(this.mapLayer);
        this.transformLayer.on('tick', function() {
            this.scaleX = this.scaleY = alias.scale;
        });
        this.translateLayer = new createjs.Container();
        this.translateLayer.addChild(this.transformLayer);

        this.stage.addChild(this.translateLayer);

        this.roads = {};
        this.dots = {};
        this.icons = {};
        this.grid_cache = {};

        this.handler = function(id, type) {};

        this.isZooming = false;

        this.controls = {drag: false, x: 0, y: 0};
        this.defaultView = {x:0, y: 0, scale: 1};

        this.stage.on('stagemousedown', function(e) {
            if (e.nativeEvent.which == 1)
                alias.controls = {drag: true, x: e.stageX, y: e.stageY};
        });
        this.stage.on('stagemouseup', function(e) {
            alias.controls = {drag: false, x: 0, y: 0};
        });
        this.stage.on('stagemousemove', function(e) {
            if (!alias.controls.drag) return;
            alias.scroll(e.stageX - alias.controls.x, e.stageY - alias.controls.y);

            alias.controls = {drag: true, x: e.stageX, y: e.stageY};
        });
    };

    Gamemap.prototype = Object.create(CanvasAnimationModule.prototype);

    Gamemap.prototype.setHandler = function(h) {
        this.handler = h;
    };

    Gamemap.prototype.render = function() {
        this.title = new createjs.Text(this.data.mapname, "70px Arial", "#ff7700");
        this.title.alpha = 0;

        this.stage.addChild(this.title);
        createjs.Tween.get(this.title, {loop: false})
            .to({y: 0, x: (this.dimensions[0] - this.title.getBounds().width)/2, alpha: 0}, 0)
            .to({y: 24, alpha: 0.2}, 1200);

        this.renderRoads();
        this.renderIcons();
    };

    Gamemap.prototype.scroll = function(x, y) {
        this.translateLayer.x += x;
        this.translateLayer.y += y;
    };

    Gamemap.prototype.zoom = function(factor) {
        if (this.isZooming) return;

        var alias = this;

        var zoomscale;
        if (this.defaultView.scale > this.scale) zoomscale = 0.5;
        else if (this.defaultView.scale < this.scale) zoomscale = 1.5;
        else zoomscale = (factor > 0) ? 1.5 : 0.5;
        this.isZooming = true;

        if (factor == 0) {
            createjs.Tween.get(this, {loop: false})
                .to({scale: this.defaultView.scale}, 200, createjs.Ease.backOut);
            createjs.Tween.get(this.translateLayer, {loop: false})
                .to({x: this.defaultView.x, y: this.defaultView.y}, 200, createjs.Ease.backOut)
                .call(function() {alias.isZooming = false;});

        } else {
            var to_scale = Math.max(Math.min(this.scale + zoomscale * factor, 6 * this.defaultView.scale), 0.5 * this.defaultView.scale);
            var zfc = to_scale/this.scale;
            var b = this.translateLayer.getBounds();

            createjs.Tween.get(this.translateLayer, {loop: false})
                .to({x: this.translateLayer.x - (b.width * (zfc - 1))/2, y: this.translateLayer.y - (b.height * (zfc - 1))/2}, 200)
                .call(function() {alias.isZooming = false;});

            createjs.Tween.get(this, {loop: false})
                .to({scale: to_scale}, 200);
        }
    };

    Gamemap.prototype.load = function() {

        var alias = this;

        $.each(this.data.locations, function(k, loc) {
            alias.addResource('places/' + loc.icon);
        });
        for (var i = 1; i <= <?=Model_Map_Abstract::MMA_NUMBER_OF_TAGS?>; i++)
            alias.addResource('places/tags/tag_' + i + '.gif')

    };
})();