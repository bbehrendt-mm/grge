goog.provide('game');

game = { 
    temp: {},
    clean: function() {
        game.temp = {};
        game.render.html.modal.unblend(0, true);
    },

    lang: function(set) {
        var cfg = game.storage.get('settings','language',false);

        if (!cfg && !set) {
            //Fix stoopid IE
            if (!navigator.language) {
                if (navigator.browserLanguage) navigator.language = navigator.browserLanguage;
                else if (navigator.userLanguage) navigator.language = navigator.userLanguage;
                else navigator.language = 'en-en';
            }

            game.storage.set('settings','language',navigator.language);
            return navigator.language;
        } else if (set) {
            game.storage.set('settings','language',set);
            return set;
        } else return cfg;
    }
};