core.popup = {
    spawn: function(dx,dy) {

        var exp = function(w) {
            if (typeof w == 'undefined')
                w = 'auto';
            if (typeof w !== 'object')
                w = {desktop: w, lg: w, md: w, sm: w};

            w.desktop = w.desktop || w.lg || w.md || w.sm;
            w.lg = w.lg || w.desktop || w.md || w.sm;
            w.md = w.md || w.lg || w.sm || w.desktop;
            w.sm = w.sm || w.md || w.lg || w.desktop;
            return w;
        };

        var gt = function(w, ref) {
            var decoded;
            if (typeof w !== 'object') decoded = w;
            else if (!game.mobile) decoded = w.desktop;
            else decoded = w[game.mobile];

            if (typeof decoded == "string") {

                if (decoded == 'auto') {}
                else if (decoded[decoded.length-1] == '%')
                    decoded = ref * (parseFloat(decoded.substr(0,decoded.length-1)/100));
                else decoded = parseFloat(decoded);

            }

            return decoded;
        };

        dx = exp(dx);
        dy = exp(dy);

        var wrapper = $('<div />').addClass('popup-wrapper').appendTo($('body').css('overflow','hidden'));

        var popup = $('<div />').addClass('popup')
            .on('reposition', function() {

                var ldx = gt(dx, $(window).width());
                var ldy = gt(dy, window.innerHeight);

                $(this).css({
                    top: ldy == 'auto' ? 30 : (((ldy + 60) > window.innerHeight) ? 0 : 60),
                    left: ldx == 'auto' ? 0 : Math.max(0,$(window).width()/2 - ldx/2),
                    height: ldy,
                    width: ldx
                });
            }).trigger('reposition').appendTo(wrapper);

        var z = game.render.html.modal.blend(function() {
            popup.trigger('close').addClass('disabled').css({
                '-webkit-filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)',
                'filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: game.s.quality() > 1 ? 'scale(1.5)' : 'scale(1)'
            }, 400, 'swing', function() {
                $(this).parent().remove();
                if (!$('.popup').length)
                    $('body').css('overflow','auto');
            });
        }, true, true);

        wrapper.css('z-index',z+1);

        popup.on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).css({
            opacity: 0,
            transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)',
            'transition': 'filter 0.4s ease, -webkit-filter 0.4s ease',
            '-webkit-filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)',
            'filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)'
        }).animate({
            opacity: 1,
            transform: 'scale(1)'
        }, 500).css({
            '-webkit-filter': '',
            'filter': ''
        });

        return popup;
    },

    genericFilterLoader: function(callback, typeFilterData) {
        var popup = core.popup.spawn({desktop: 500, md: '100%'},{desktop: 300, md: '100%'});

        var frame = NF.row().appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 0,
                bottom: 36,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = NF.row().css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 36,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn').text(<?=__j('Abbrechen');?>).appendTo($('<div />').addClass('cell rw-4 rw-sm-6').appendTo(bottom)).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn').text(<?=__j('Anwenden');?>).appendTo($('<div />').addClass('cell ro-4 rw-4 rw-sm-6 ro-sm-0').appendTo(bottom)).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters, class_cell;
        frame.append(
            NF.row().append(
                $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text(<?=__j('Status')?>)).append(typefilters = NF.row()))
            ).append(
                class_cell = $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text(<?=__j('Kategorie')?>)).append(classfilters = NF.row()))
            )
        );

        $.each(typeFilterData, function(id, obj) {
            if (id == 'categories') return;
            var chk;
            typefilters.append(
                $('<div />').addClass('cell rw-6 rw-sm-12').append(
                    $('<label />').text(obj.name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('tid', id).prop('checked', obj.active).click(function() {
                            typeFilterData[id].active = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
        });

        var has_cats = false;
        $.each(typeFilterData.categories, function(name, active) {
            var chk;
            classfilters.append(
                $('<div />').addClass('cell rw-4 rw-sm-6').append(
                    $('<label />').text(name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('cid', name).prop('checked', active).click(function() {
                            typeFilterData.categories[name] = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
            has_cats = true;
        });

        if (!has_cats)
            class_cell.hide();
        else class_cell.show();
    },

    genericBlueprintLoader: function(type, popup, data, frame, filters, close) {
        if (!popup) popup = core.popup.spawn({desktop: 700, lg: '100%'},{desktop: 450, lg: '100%'});

        if (!frame || !filters || !close) {
            popup.empty();

            frame = NF.row().appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: 24,
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            ).data('type-filters', {
                    'impossible': {active: false, name: <?=__j('Unmögliche Projekte')?>},
                    'locked': {active: true, name: <?=__j('Gesperrte Projekte')?>},
                    'possible': {active: true, name: <?=__j('Vorbereitete Projekte')?>},
                    'ready': {active: true, name: <?=__j('Mögliche Projekte')?>},
                    'done': {active: true, name: <?=__j('Abgeschlossene Projekte')?>},
                    'categories': {}
            }).on('filter', function() {
                    var typefilters = $(this).data('type-filters');

                    $(this).find('.blueprint').parent().hide();

                    var alias = $(this);
                    var all_enabled = true;
                    $.each(typefilters.categories, function(name, active) {
                        if (active)
                            alias.find('.blueprint[data-cats*="|' + name + '|"]').parent().show();
                        else all_enabled = false;
                    });
                    if (all_enabled) alias.find('.blueprint[data-cats="||"]').parent().show();

                    if (!typefilters.impossible.active)  $(this).find('.blueprint.red').parent().hide();
                    if (!typefilters.locked.active)      $(this).find('.blueprint.plain').parent().hide();
                    if (!typefilters.possible.active)    $(this).find('.blueprint.green').parent().hide();
                    if (!typefilters.ready.active)       $(this).find('.blueprint.green.active').parent().hide();
                    if (!typefilters.done.active)        $(this).find('.blueprint.blue').parent().hide();

                    $(this).find('.blueprint-group').each(function() {
                        $(this).show();
                        if (!$(this).find('.blueprint:visible').length) $(this).hide();
                    });
            });

            NF.row().append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text(<?=__j('Angezeigte Projekte filtern...')?>).click(function() {
                core.popup.genericFilterLoader(function(a) {
                    frame.data('type-filters', a).trigger('filter');
                }, JSON.parse(JSON.stringify(frame.data('type-filters'))));
            }));
            close.append($('<div />').addClass('btn small').append($('<i/>').addClass('fa fa-times')).click(function() {
                popup.trigger('unpop');
            }));
        }

        var build_func = function(bdata) {
            frame.empty();
            var categories = {}; var cat_count = 0;
            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;

                if (!$.objToArray(v.categories).length) v.categories = [<?=__j('Sonstiges')?>];
                $.each(v.categories, function(i,cat) {
                    if (!categories[cat]) cat_count++;
                    categories[cat] = true;
                });
            });

            if (cat_count > 1)
                $.each(categories, function(n,t) {
                    frame.append($('<div />').attr('data-group-cat',n).addClass('blueprint-group').append($('<b />').addClass('header').text(n)).append($('<div />').addClass('blueprint-group-target row')));
                });

            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;
                if (!$.objToArray(v.categories).length) v.categories = [<?=__j('Sonstiges')?>];
                var targets = $();
                if (cat_count > 1) $.each(v.categories, function(i,cat) {targets = targets.add(frame.find('div[data-group-cat="' + cat + '"]').find('.blueprint-group-target'));});
                else targets = frame;

                targets.append($('<div />').addClass('cell padded rw-4 rw-md-6').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
                    if (v.confirm && !window.confirm(game.i18n(v.confirm, {':name': v.name})))
                        return;

                    var prev_scroll = $('.popup').find('>*:first-child').scrollTop();
                    popup.addClass('disabled');
                    core.command('location/' + type, {build: k}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        frame.trigger('filter');
                        $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        popup.off('close').on('close', function() {
                            setTimeout(function() {
                                core.command();
                            }, 100);
                        })
                    }, true)
                }, popup)));
            });

            var tf = frame.data('type-filters');
            $.each(categories, function(name, t) {
                tf.categories[name] = typeof tf.categories[name] !== "undefined" ? tf.categories[name] : true;
            });
            frame.data('type-filters', tf).trigger('filter');
        };

        if (!data) {
            frame.append(core.snippets.wait());
            core.command('location/' + type, {}, true, build_func);
        } else build_func(data);
    },

    builder: function() {
        core.popup.genericBlueprintLoader('builder');
    },
    maker: function() {
        core.popup.genericBlueprintLoader('maker');
    },
    fighter: function() {
        core.popup.genericBlueprintLoader('fighter');
    },
};