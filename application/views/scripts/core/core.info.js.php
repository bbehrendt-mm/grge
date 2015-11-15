(function() {

    core.parts.info = function(data, target) {
        var details = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-6 ro-3 rw-lg-8 ro-lg-2 rw-md-12 ro-md-0 padded').appendTo(target));

        details.append($('<h3 />').text(<?=__j('Aktuelles Spiel')?>))
            .append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Spielmodus')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.mode))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Beruf')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.job))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Level')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.level))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Spieldauer')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.gametime))
            ).append(data.gametime == data.lifetime ? false : NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Lebensdauer')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.lifetime))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Punkte')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.points))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Getötete Zombies')?>))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.kills))
            );
    };
})();