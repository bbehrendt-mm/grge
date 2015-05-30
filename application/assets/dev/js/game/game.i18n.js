goog.provide('game.i18n');
goog.require('game');

game.i18n = function(str, replace) {
    if (replace)
        $.each(replace, function(k,v) {
            str = str.replace(k,v);
        });
    return str;
};

game.short = function(str, breakAt) {
    return (str.length > (breakAt + 1)) ? (str.substring(0,breakAt) + '…') : str;
};