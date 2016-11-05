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
        if (typeof pos == 'object')
            switch (game.mobile) {
                case 'lg':  pos = pos['lg'] || pos['desktop'] || pos['md'] || pos['sm']; break;
                case 'md':  pos = pos['md'] || pos['lg'] || pos['sm'] || pos['desktop']; break;
                case 'sm':  pos = pos['sm'] || pos['md'] || pos['lg'] || pos['desktop']; break;
                default:    pos = pos['desktop'] || pos['lg'] || pos['md'] || pos['sm']; break;
            }

        switch (pos) {
            case 'none': return false;
            case 'left': return {my: 'right center', at: 'left center'};
            case 'right':return {my: 'left center', at: 'right center'}; break;
            case 'top': return {my: 'bottom center', at: 'top center'}; break;
            case 'bottom': return {my: 'top center', at: 'bottom center'}; break;
        }
    },

    generic: function(pos, classes,interactable, delay, events, viewport) {
        var m = game.render.html.qtip.posify(pos);

        if (!events) events = {};

        if (!events.show)
            events.show = function(event,api) {
                var m_live = game.render.html.qtip.posify(pos);
                if (!m_live) event.preventDefault();
                else api.set({
                    'position.my': m_live.my,
                    'position.at': m_live.at
                });
            };

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
                my: m ? m.my : 'bottom center',
                at: m ? m.at : 'top center',
                viewport: viewport ? viewport : $(window),
                container: $('body'),
                adjust: {
                    method: 'shift flipinvert'
                }
            },
            events: events
        }
    },

    help: function(pos, events, smallpad) {
        return game.render.html.qtip.generic(pos,'qtip-tipsy qtip-shadow qtip-rounded qtip-custom-help' + (smallpad ? '2' : ''),false,0,events);
    },

    player: function(pos) {
        return game.render.html.qtip.generic(pos,'qtip-tipsy qtip-shadow qtip-rounded');
    },

    store: function(pos, title, description) {
        return game.render.html.qtip.generic(pos,'qtip-tipsy qtip-custom-store', false, 0, {
            render: function(event,api) {
                $(this).find('.qtip-content')
                    .empty()
                    .append($('<b />').addClass('header').text(title))
                    .append($('<p />').text(description))
            }
        });
    },

    ingame: function(pos, events,viewport) {
        return game.render.html.qtip.generic(pos,'qtip-default qtip-shadow qtip-custom-ingame',true, 500, events,viewport);
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