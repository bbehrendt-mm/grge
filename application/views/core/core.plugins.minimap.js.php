(function() {
    core.plugins.Minimap = function(canvas) {
        this.canvas = $(canvas).get(0);
        this.size = Math.min(canvas.width, canvas.height);
        this.environments = [];
        this.status = 0;
        this.overlay = null;
        this.player = null;
        this.autorender = false;
        this.images = {final: false, count: 0, ready: 0, cache: []};
        if (!this.initialize())
            alert('plugin:minimap init failed');
    };

    core.plugins.Minimap.prototype.requestImage = function(path, last_one) {
        var alias = this;
        if (this.images.cache[path]) return true;
        if (last_one) this.images.final = true;

        this.images.count++;
        this.images.cache[path] = new Image();
        this.images.cache[path].addEventListener("load", function() {
            alias.images.ready++;
            if (alias.images.final && alias.images.count == alias.images.ready) {
                alias.status = 2;
                if (alias.autorender) alias.updateRenderer();
            }
        }, false);
        this.images.cache[path].src = path;
    };

    core.plugins.Minimap.prototype.getImage = function(path) {
        if (!this.images.cache[path]) return null;
        return this.images.cache[path];
    };

    /**
     * Returns true if the module is fully initialized
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.is_initialized = function() {
        return this.status >= 2;
    };

    /**
     * Returns true if the module is currently initializing. Will also return true if the module is already initialized and allow_initialized is true.
     * @param [allow_initialized] {boolean} If true, the module having been initialized already will cause this function to return true as well.
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.is_initializing = function(allow_initialized) {
        return this.status == 1 || (allow_initialized && this.is_initialized());
    };

    core.plugins.Minimap.prototype.postProcessing = function(imgData) {
        return imgData;
    };

    core.plugins.Minimap.prototype.reset = function(canvas, keepSurroundingData) {
        this.stage.enableDOMEvents(false);
        this.stage.canvas = $(canvas).get(0);
        this.stage.enableDOMEvents(true);

        if (!keepSurroundingData)
            $.map(this.environments, function(v) {
                return (v.x == v.y && v.x == 0) ? v : null;
            });
    };

    core.plugins.Minimap.prototype.startStop = function(start = false) {
        if (start) {
            createjs.Ticker.removeAllEventListeners('tick');
            var alias = this;
            createjs.Ticker.timingMode = 'synched';
            createjs.Ticker.framerate = 60;
            createjs.Ticker.addEventListener("tick", function() {
                alias.stage.update();
                var ctx = $(alias.canvas).get(0).getContext('2d');
                ctx.putImageData(alias.postProcessing(ctx.getImageData(0,0,alias.size,alias.size)),0,0);
            });
        } else createjs.Ticker.reset();
    };

    /**
     * Initializes the module and loads missing assets
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.initialize = function() {
        var alias = this;

        if (this.is_initializing(true)) return true;

        this.status = 1;
        this.stage = new createjs.Stage($(this.canvas).attr({height: this.size, width: this.size}).get(0));
        this.stage.regX = this.stage.regY = .5;

        this.requestImage('media/icons/minimap/floor.png', true);

        this.startStop(true);
        return true;
    };

    /**
     * Adds a screen to the map at the specified position
     * @param pos_x {int} X position
     * @param pos_y {int} Y position
     * @param top {int} North corridor ID (or 0 to omit)
     * @param bottom {int} South corridor ID (or 0 to omit)
     * @param left {int} West corridor ID (or 0 to omit)
     * @param right {int} South corridor ID (or 0 to omit)
     * @returns {core.plugins.Minimap}
     * @param zombies {int} Number of zombies
     * @param players {int} Number of other players
     */
    core.plugins.Minimap.prototype.addEnvironment = function(pos_x, pos_y, top, bottom, left, right, zombies, players) {
        $.map(this.environments, function(v) {
            return (v.x == pos_x && v.y == pos_y) ? null : v;
        });

        this.environments.push({x: pos_x, y: pos_y, container: null, top: top, bottom: bottom, left: left, right: right, zombies: zombies, players: players, size: (top || bottom || left || right) ? 0.25 : 0.65});
        this.updateRenderer();
        return this;
    };

    /**
     * Shifts the view by the given coordinates
     * @param x {int} X Shift
     * @param y {int} Y Shift
     * @param duration {int} Animation duration
     * @param [callback] {function} Callback function (called after animation finishes)
     * @returns {core.plugins.Minimap}
     */
    core.plugins.Minimap.prototype.shift = function(x, y, duration, callback) {
        var alias = this;
        setTimeout(callback, duration);

        createjs.Tween.get(this.player)
            .to({x: this.player.x + (x * 0.1 * this.size), y: this.player.y + (y * 0.1 * this.size)}, duration/5, createjs.Ease.getPowInOut(2))
            .to({x: this.player.x - (x * 0.2 * this.size), y: this.player.y - (y * 0.2 * this.size)}, duration/2, createjs.Ease.getPowInOut(2))
            .to({x: this.player.x, y: this.player.y}, duration/2, createjs.Ease.getPowInOut(2));

        $.each(this.environments, function(k,v) {
            createjs.Tween.get(v.container)
                .to({ x: alias.size * (v.x - x), y: alias.size * (v.y - y)}, duration, createjs.Ease.getPowInOut(4))
                .call(function() {
                    v.x -= x;
                    v.y -= y;
                });
        });
        return this;
    };

    /**
     * Updates the stage. Run this function after adding elements.
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.updateRenderer = function() {
        if (!this.is_initialized())
            return this.autorender = true;

        var alias = this;

        if (!this.overlay) {
            this.overlay = new createjs.Container();
            var lense_shadow = new createjs.Shape();
            lense_shadow.graphics.beginRadialGradientFill(['rgba(0,0,0,0)','rgba(0,0,0,1)'],[0,1],this.size/2,this.size/2,this.size/6,this.size/2,this.size/2,this.size/1.3).rect(0,0,alias.size,alias.size);

            this.player = new createjs.Bitmap('media/icons/minimap/citizen.png');
            this.player.x = this.player.y = this.size/2 - 12;


            this.overlay.addChild(lense_shadow, this.player);
        }

        $.each(this.environments, function(k,v) {
            var c_width = alias.size * v.size;
            var ceil_width = alias.size/20;
            var wall_height = alias.size/8;
            var m = alias.size/2 - c_width/2;

            if (!v.container) {
                v.container = new createjs.Container();

                var floor = new createjs.Shape();
                floor.graphics.beginBitmapFill(alias.getImage('media/icons/minimap/floor.png')).rect(0,0,alias.size,alias.size);

                var walls = new createjs.Shape();
                walls.graphics.beginFill('#000000')
                    .rect(0, 0, m + (v.top ? 0 : c_width), m)
                    .rect(0, alias.size, m, -m - (v.left ? 0 : c_width))
                    .rect(alias.size, alias.size, -m - (v.bottom ? 0 : c_width), -m)
                    .rect(alias.size, 0, -m, m + (v.right ? 0 : c_width));

                v.container.addChild(floor, walls);

                var dots = {
                    topleft: [m,m - wall_height],
                    topright: [alias.size-m,m - wall_height],
                    bottomleft: [m,alias.size-m],
                    bottomright: [alias.size-m,alias.size-m]
                };

                var frameColor = '#462D21';
                var wallColors = ['#00934C','#00D37A'];

                //Columns
                walls.graphics.beginFill(frameColor)
                    .rect(dots.topleft[0],dots.topleft[1],          -ceil_width,-ceil_width)
                    .rect(dots.topright[0],dots.topright[1],        ceil_width,-ceil_width)
                    .rect(dots.bottomleft[0],dots.bottomleft[1],    -ceil_width,ceil_width)
                    .rect(dots.bottomright[0],dots.bottomright[1],  ceil_width,ceil_width);

                if (v.top) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.topleft[0],dots.topleft[1],      -ceil_width, -(m - wall_height))
                        .rect(dots.topright[0],dots.topright[1],    ceil_width, -(m - wall_height));
                } else {
                    walls.graphics.beginFill(frameColor).rect(dots.topleft[0] - ceil_width, dots.topleft[1], c_width + 2* ceil_width, -ceil_width);
                    walls.graphics.beginLinearGradientFill([wallColors[0],wallColors[1],wallColors[1],wallColors[0]], [0,0.1,0.9,1],0,m - wall_height,0,m).rect(dots.topleft[0], dots.topleft[1], c_width, wall_height);
                }

                if (v.left) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.topleft[0],dots.topleft[1],          -m, -ceil_width)
                        .rect(dots.bottomleft[0],dots.bottomleft[1],    -m, ceil_width);
                    walls.graphics.beginLinearGradientFill([wallColors[0],wallColors[1],wallColors[1],wallColors[0]], [0,0.1,0.9,1],0,m - wall_height,0,m).rect(dots.topleft[0], dots.topleft[1],-m, wall_height);
                } else walls.graphics.beginFill(frameColor).rect(dots.topleft[0], dots.topleft[1] - ceil_width, -ceil_width, c_width + 2* ceil_width + wall_height);

                if (v.bottom) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.bottomleft[0],dots.bottomleft[1],      -ceil_width, m)
                        .rect(dots.bottomright[0],dots.bottomright[1],    ceil_width, m);
                } else walls.graphics.beginFill(frameColor).rect(dots.bottomleft[0] - ceil_width, dots.bottomleft[1], c_width + 2* ceil_width, ceil_width);

                if (v.right) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.topright[0],dots.topright[1],          m, -ceil_width)
                        .rect(dots.bottomright[0],dots.bottomright[1],    m, ceil_width);
                    walls.graphics.beginLinearGradientFill([wallColors[0],wallColors[1],wallColors[1],wallColors[0]], [0,0.1,0.9,1],0,m - wall_height,0,m).rect(dots.topright[0], dots.topright[1],m, wall_height);
                } else walls.graphics.beginFill(frameColor).rect(dots.topright[0], dots.topright[1] - ceil_width, ceil_width, c_width + 2* ceil_width + wall_height);

                var i;
                var actors = [];
                for (i = 0; i < v.zombies + v.players; i++) {
                    var actor = new createjs.Bitmap(i >= v.zombies ? 'media/icons/minimap/citizen.png' : 'media/icons/minimap/zombie.png');
                    actor.x = m + Math.random() * (c_width - 24);
                    actor.y = m + Math.random() * (c_width - 24);
                    actors.push(actor);
                }
                actors.sort(function(a,b) {return a.y - b.y});

                $.each(actors, function(id, actor) {v.container.addChild(actor);});

                v.container.x = v.x * alias.size;
                v.container.y = v.y * alias.size;

                alias.stage.addChild(v.container);
            }
        });

        this.stage.addChild(this.overlay);
        return true;
    }
})();