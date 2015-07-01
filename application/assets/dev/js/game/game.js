goog.provide('game');

game = { 
    temp: {},

    mobile: false,
    vcsid: null,

    touch: function() {
        var cfg = game.storage.get('settings','input-device');
        if (cfg == 'touch') return true;
        else if (cfg == 'mouse') return false;
        else return typeof window.ontouchstart != "undefined";
    },

    clean: function(skip_temp) {
        if (!skip_temp) {
            game.temp = {};
            $('.canvasHTML').remove();
        }
        game.render.html.modal.unblend(0, true);
    },

    init: function() {
        $(window).on('resize', function() {
            var w = $(window).width();
            if (w >= 900)       game.mobile = false;
            else if (w >= 600)  game.mobile = 'lg';
            else if (w >= 480)  game.mobile = 'md';
            else                game.mobile = 'sm';
            $('.popup').trigger('reposition');
        }).trigger('resize');

        game.update_ui_quality();
    },

    registerVirtualCookie: function(sid) {
        // Check if the site has accepted our cookie
        var name = 'evolution='; var csid = '';
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) {
                csid = c.substring(name.length,c.length);
                break;
            }
        }

        // If not, create a virtual cookie using the JS environment
        if (csid != sid) {
            this.vcsid = sid;
            console.warn('Cookie support of your browser seems to be broken. We\'ll have to work around that...', csid, sid);
        }

    },

    lang: function(set) {
        var cfg = game.storage.get('settings','language',false);

        if (!cfg && !set) {
            //Fix stoopid IE
            if (!navigator.language) {
                if (navigator.browserLanguage) navigator.language = navigator.browserLanguage;
                else if (navigator.userLanguage) navigator.language = navigator.userLanguage;
                else navigator.language = 'en';
            }

            game.storage.set('settings','language',navigator.language);
            return navigator.language;
        } else if (set) {
            game.storage.set('settings','language',set);
            return set;
        } else return cfg;
    },

    reset: function() {
        window.location.href = 'index.php';
    },

    update_ui_quality: function() {
        var q = game.s.quality();


        if (q <= 2) $('body').addClass('q-no-blur');
        else $('body').removeClass('q-no-blur');

        if (q <= 1) $('body').addClass('q-low');
        else $('body').removeClass('q-low');

        $.fx.off = (q <= 1);
    },

    s: {
        quality: function() {return game.storage.get('settings','ui-quality', game.mobile ? 2 : 3)}
    },

    w: {
        quality: function(v) {
            game.storage.set('settings','ui-quality', v);
            game.update_ui_quality();

        }
    }
};