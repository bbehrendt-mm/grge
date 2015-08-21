(function() {
    var create_blob = function(pos, size, jitter) {
        var img = Math.round(Math.random() * (this.splatter_count-1) + 1);
        img = this.getResource('splatter/splat' + img + '.png');

        var container = new createjs.Container();
        container.x = pos.x + Math.round(Math.random() * jitter);
        container.y = pos.y + Math.round(Math.random() * jitter);

        var bitmap = new createjs.Bitmap(img);
        bitmap.x = -Math.round(img.width/2);
        bitmap.y = -Math.round(img.height/2);

        container.addChild(bitmap);
        container.scaleX = size/img.width;
        container.scaleY = size/img.height;
        container.rotation = Math.random() * 360;

        this.splatlayer.addChild(container);
    };

    Battle.prototype.splatter_blob = function(pos, size, jitter) {
        create_blob.call(this, pos, size, jitter);
        this.splatlayer.updateCache();
    };

    Battle.prototype.splatter_line = function(pos, vec, amount, size, max_spread) {
        var step = {x: vec.x/amount, y: vec.y/amount};

        for (var i = 0; i < amount; i++)
            create_blob.call(this, {x: pos.x + i * step.x, y: pos.y + i * step.y}, size, (i/amount) * max_spread);
        this.splatlayer.updateCache();
    }
})();