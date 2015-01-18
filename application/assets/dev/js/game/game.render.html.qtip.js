goog.provide('game.render.html.qtip');

goog.require('game');
goog.require('game.render');
goog.require('game.render.html');

game.render.html.qtip = {
    lang: function() {
        return {
            style: {
                classes: 'qtip-tipsy qtip-shadow qtip-rounded'
            },
            position: {
                my: 'top left',
                at: 'bottom center'
            }
        }
    },

    posify: function(pos) {
        switch (pos) {
            case 'left': return {my: 'right center', at: 'left center'};
            case 'right':return {my: 'left center', at: 'right center'}; break;
            case 'top': return {my: 'bottom center', at: 'top center'}; break;
            case 'bottom': return {my: 'top center', at: 'bottom center'}; break;
        }
    },

    generic: function(pos, classes,interactable, delay, events) {
        var m = game.render.html.qtip.posify(pos);
        return {
            style: {
                classes: classes
            },
            show: {
                delay: delay
            },
            hide: {
                fixed: interactable,
                delay: interactable ? 100 : 0
            },
            position: {
                my: m.my,
                at: m.at,
                viewport: $(window),
                container: $('body'),
                adjust: {
                    method: 'shift none'
                }
            },
            events: events
        }
    },

    help: function(pos) {
        return game.render.html.qtip.generic(pos,'qtip-tipsy qtip-shadow qtip-rounded qtip-custom-help');
    },

    player: function(pos) {
        return game.render.html.qtip.generic(pos,'qtip-tipsy qtip-shadow qtip-rounded');
    },

    ingame: function(pos, events) {
        return game.render.html.qtip.generic(pos,'qtip-default qtip-shadow qtip-custom-ingame',true, 0, events);
    },

    map: function(target, events) {
        return {
            style: {
                classes: 'qtip-default qtip-youtube qtip-shadow'
            },
            show: {
                ready: true,
                event: 'none',
                delay: 0
            },
            position: {
                my: 'top center',
                at: 'bottom center',
                viewport: $(window),
                container: $('body'),
                target: target,
                adjust: {
                    method: 'shift none'
                }
            },
            events: $.extend(events, {
                show: function(event) {
                    event.preventDefault();
                }
            })
        }
    }
};