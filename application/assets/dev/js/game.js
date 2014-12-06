goog.provide('game');

game = { 
    temp: {},
    clean: function(skip_temp) {
        if (!skip_temp) {
            game.temp = {};
            $('.canvasHTML').remove();
        }
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
    },

    reset: function() {
        document.location.href = "index.php";
    }
};

//jQuery Overrides
(function() {

    var injectCleaner = function(jqFuncName) {
        var backup = jQuery.fn[jqFuncName];
        jQuery.fn[jqFuncName] = function() {
            $(this).find('*[data-hasqtip]').qtip('destroy',true);
            return backup.apply(this,arguments);
        };
    };

    $.each(['html','empty','remove'],function(k,v) {injectCleaner(v)});
})();