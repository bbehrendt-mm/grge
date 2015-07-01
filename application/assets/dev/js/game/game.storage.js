goog.require('game');
goog.provide('game.storage');

game.storage = {
    get: function(section, subsection, standart) {
        var ret = JSON.parse(localStorage.getItem('grge.' + section + '.' + subsection));
        if (!ret) return standart;
        else return ret;
    },

    set: function(section, subsection, data) {
        return localStorage.setItem('grge.' + section + '.' + subsection,JSON.stringify(data));
    }
};