goog.provide('game.render.canvas');
goog.require('game');
goog.require('game.render');

game.render.canvas = {

    drawHTML: function(html, canvas, callback, error_callback) {
        rasterizeHTML.drawHTML($('head').html() + '<link rel="stylesheet" type="text/css" href="../../../css/zombvival.canvas.css" />' + html, $(canvas).get(0)).then(function() {callback($(canvas).get(0))}, error_callback);
    },

    copyHTML: function(element, canvas, callback, error_callback) {
        var c = $(canvas).get(0);

        c.width = $(element).width();
        c.height = $(element).height();
        var offset = $(element).offset();

        var canvas_tmp = $('<canvas height="' + $(window).height() + '" width="' + $(window).width() + '"></canvas>').get(0);
        rasterizeHTML.drawDocument(document, canvas_tmp).then(function() {
            var pixels = canvas_tmp.getContext('2d').getImageData(Math.round(offset.left), Math.round(offset.top), c.width, c.height);
            c.getContext('2d').putImageData(pixels,0,0);
            if (callback instanceof Function)
                callback();
        }, error_callback);
    },

    canvasizeHTML: function(element, callback, error_callback) {
        var canvas = $('<canvas class="canvasHTML"></canvas>').get(0);
        var offset = $(element).offset();

        game.render.canvas.copyHTML(element, canvas, function() {
            $(canvas).css({
                position: 'absolute',
                top: Math.round(offset.top),
                left: Math.round(offset.left) + 11
            }).appendTo($('body'));
            $(element).css('opacity', 0);

            if (callback instanceof Function)
                callback(canvas);
        }, error_callback)
    },

    backup: function(canvas) {
        var c = $(canvas);
        var raw = c.get(0);

        var pixels = raw.getContext('2d').getImageData(0, 0, raw.width, raw.height);
        c.on('grge.canvas-reset', function() {
            raw.getContext('2d').putImageData(pixels,0,0);
        });
    },

    restore: function(canvas) {
        $(canvas).trigger('grge.canvas-reset');
    },

    distort: {

        apply: function(canvas, filter) {
            var raw = $(canvas).get(0);
            var context = raw.getContext('2d');
            context.putImageData(filter(context.getImageData(0,0,raw.width,raw.height)),0,0);
        },

        filters: {
            brightness: function(delta) {
                return function(pixels) {
                    var d = pixels.data;

                    if (delta == 0)
                        return pixels;

                    for (var i = 0; i < d.length; i += 4) {
                        d[i] += delta;
                        d[i + 1] += delta;
                        d[i + 2] += delta;
                    }
                    return pixels;
                }
            },

            analog: function(delta, colorize) {
                return function(pixels) {
                    var d = pixels.data;

                    if (delta == 0)
                        return pixels;

                    for (var i = 0; i < d.length; i += 4) {
                        var c1 = (Math.random() * delta * 2) - delta;
                        d[i] += c1;
                        d[i + 1] += colorize ? ((Math.random() * delta * 2) - delta) : c1;
                        d[i + 2] += colorize ? ((Math.random() * delta * 2) - delta) : c1;
                        d[i + 3] += (Math.random() * delta);
                    }
                    return pixels;
                }
            },

            mpegDistortion: function(blocksize, strength, odd) {
                return function(pixels) {
                    var d = pixels.data;

                    if (strength == 0)
                        return pixels;
                    var col = [Math.random() > 0.8 ? .5 : 1, Math.random() > 0.8 ? .5 : 1, Math.random() > 0.8 ? .5 : 1];

                    for (var i = 0; i < d.length - 4*strength; i += 4) {
                        var x = Math.floor(i/4)%250;
                        var y = Math.floor(Math.floor(i/4)/250);

                        if (odd == (Math.floor(x/blocksize)%2 == Math.floor(y/blocksize)%2)) {
                            d[i] = col[0] * d[i+4*strength];
                            d[i + 1] = col[1] * d[i+4*strength + 1];
                            d[i + 2] = col[2] * d[i+4*strength + 2];
                            d[i + 3] = d[i+4*strength + 3];
                        }
                    }
                    return pixels;
                }
            }
        }
    }
};