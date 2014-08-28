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

    help: function($pos) {
        var m = {my: 'right center', at: 'left center'};
        switch ($pos) {
            case 'left': m = {my: 'right center', at: 'left center'}; break;
            case 'right': m = {my: 'left center', at: 'right center'}; break;
            case 'top': m = {my: 'bottom center', at: 'top center'}; break;
            case 'bottom': m = {my: 'top center', at: 'bottom center'}; break;
        }
        return {
            style: {
                classes: 'qtip-tipsy qtip-shadow qtip-rounded qtip-custom-help'
            },
            position: {
                my: m.my,
                at: m.at,
                viewport: $(window),
                container: $('#content'),
                adjust: {
                    method: 'shift none'
                }
            }
        }
    }
};