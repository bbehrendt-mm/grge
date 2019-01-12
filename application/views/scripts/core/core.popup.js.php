core.popup = {
    spawn_window : function(title, close_button, dx, dy) {
        return NF.n('div').addClass('windowed').appendTo(core.popup.spawn(dx,dy).append(NF.row('head')
            .append(NF.cell(true,2,0,'left'))
            .append(NF.cell(true,8,0,'center').text(title))
            .append(NF.cell(true,2,0,'right')
                .append(close_button ? NF.fa('window-close').addClass('pointer').click(function() {
                    $(this).trigger('unpop');
                }) : null)
            )
        ));
    },

    get_window_titlebar: function(pp) {
        var selected = $();
        if      ($(pp).hasClass('popup'))    selected = $(pp).children('.row.head:first-child');
        else if ($(pp).hasClass('windowed')) return core.popup.get_window_titlebar($(pp).parent());
        else return null;

        return selected.length ? selected : null;
    },

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

    genericBlueprintLoader: function(type, room, popup, data, frame, filters, ret) {
        if (!popup) popup = core.popup.spawn_window('', true, {desktop: 700, lg: '100%'},{desktop: 450, lg: '100%'});

        var header = core.popup.get_window_titlebar(popup);

        if (!frame) popup.empty();

        if (!frame)
            frame = NF.row().appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: header.outerHeight(),
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

        if (!filters)
            header.children('.left').append(filters = NF.fa('filter').addClass('pointer').attr('title',<?=__j('Angezeigte Projekte filtern...')?>).click(function() {
                core.popup.genericFilterLoader(function(a) {
                    frame.data('type-filters', a).trigger('filter');
                }, JSON.parse(JSON.stringify(frame.data('type-filters'))));
            }).qtip(game.render.html.qtip.ingame('bottom')));

        if (!ret)
            header.children('.left').append(ret = NF.fa('chevron-left').addClass('pointer').attr('title',<?=__j('Zurück zur Übersicht')?>).click(function() {
                filters.remove();
                ret.remove();
                core.popup.rooms(popup);
            }).qtip(game.render.html.qtip.ingame('bottom')));

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
                    core.command('location/' + type, {build: k, r: bdata.room}, true, function(new_data) {
                        popup.removeClass('disabled');

                        popup.off('close').on('close', function() {
                            setTimeout(function() {
                                core.command();
                            }, 100);
                        });

                        if (type == 'tine') ret.click();
                        else {
                            core.popup.genericBlueprintLoader(type,room,popup,new_data,frame,filters,ret);
                            frame.trigger('filter');
                            $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        }

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
            core.command('location/' + type, {r: room}, true, build_func);
        } else build_func(data);
    },

    builder: function() {
        core.popup.genericBlueprintLoader('builder',0);
    },
    maker: function() {
        core.popup.genericBlueprintLoader('maker',0);
    },
    fighter: function() {
        core.popup.genericBlueprintLoader('fighter',0);
    },
    
    rooms: function(popup) {
        if (!popup) popup = core.popup.spawn_window('', true, {desktop: 700, lg: '100%'},{desktop: 450, lg: '100%'});

        popup.empty().append(core.snippets.wait());

        var titlebar = core.popup.get_window_titlebar(popup);
        titlebar.children('.center').text(<?=__j('Räume')?>);

        core.command('location/rooms', {}, true, function(data) {
            if (!data.rooms) {
                popup.trigger('unpop');
                return;
            }

            popup.empty();

            var frame = NF.row().appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: titlebar.outerHeight(),
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            );

            frame.append(NF.row().append(
                NF.cell(true, {lg: 10, md: 12}, {lg: 1, md: 0}).append(
                    NF.info(<?=__j('Hier kannst du die einzelnen Räume deines Versteckes ausbauen oder ihren Typ ändern. Außerdem kannst du mit den Ausbauten der Räume interagieren.')?>)
                )
            ));

            var main_row = NF.row().appendTo(frame);
            var rc = 0;

            $.each(data.rooms, function(id,room) {
                if (rc >= 2) {
                    rc = 0;
                    main_row = NF.row().appendTo(frame);
                }

                var room_identifier = room.name ? room.name : (room.type ? room.type : <?=__j('Unbenutzter Raum')?>);
                var room_name = room.name;
                var room_type = room.type ? room.type : <?=__j('Unbenutzter Raum')?>;
                var current, name_field, type_field;
                main_row.append(NF.cell(true,{desktop: 6, md: 12}).append(
                    current = $('<div/>').addClass('flatbox').append(
                        NF.row()
                            .append(NF.cell(false,11)
                                .append(
                                    room.name ?
                                        NF.row()
                                            .append(NF.cell(false,12,0,'center').append(name_field = $('<h3/>').text(room.name)))
                                            .append(NF.cell(false,12,0,'center').append(type_field = NF.n('div','small i').text(room_type)))
                                    :
                                        NF.row()
                                            .append(NF.cell(false,12,0,'center').append(name_field = $('<h3/>').text(room_type)))
                                            .append(NF.cell(false,12,0,'center').append(type_field = NF.n('div','small i').text('')))
                                )
                            ).append(room.options.rename ? NF.cell(true, 1,0,'center pointer').append(NF.fa('pencil-square-o')).click(function() {
                                var new_name = prompt(<?=__j('Bitte gib einen neuen Namen ein:')?>, room_name);

                                if (new_name !== null) {
                                    var alias = $(this);
                                    alias.empty().append(NF.fa('cog',true)).addClass('disabled');
                                    core.command('location/rename_room', {r: room.id, n: new_name}, true, function(data) {
                                        alias.empty().removeClass('disabled').append(NF.fa('pencil-square-o'));
                                        if (!data.success) game.render.html.notify('error',<?=__j('Ein Fehler ist aufgetreten.')?>);
                                        else {
                                            room_name = data.result;
                                            name_field.text(data.result ? data.result : room_type);
                                            type_field.text(data.result ? room_type : '')
                                        }
                                    });
                                }
                        }) : null)
                    )
                ));

                var tag_list;

                if (room.size !== null)
                    current.append(NF.row()
                        .append(NF.cell(true,4,0,  'left').append(NF.n('div','b small').text(<?=__j('Größe')?> + ': ' + room.size + 'm²')))
                        .append(NF.cell(true,4,0,'center').append(NF.n('div','b small').text(<?=__j('Freier Platz')?> + ': ' + room.free + 'm²')))
                        .append(tag_list = NF.cell(true,4,0, 'right'))
                    );
                else current.append(NF.row()
                    .append(NF.cell(true,8,0, 'left').append(NF.n('div','b small').text(<?=__j('Keine Platzbeschränkung')?>)))
                    .append(tag_list = NF.cell(true,4,0, 'right'))
                );

                if (tag_list)
                    $.each(room.tags, function(k,v) {
                        tag_list.append(NF.img('media/icons/places/rtags/' + k + '.gif').attr('title',v).qtip(game.render.html.qtip.ingame('bottom')));
                    });

                var btn_add, btn_con;
                var action_row;

                current.append(action_row = NF.row().addClass(room.options.enabled ? '' : 'disabled')
                    .append(NF.cell(true,{desktop: 6, sm: 12},0,'center').append(btn_add = NF.button(<?=__j('Ausbauen...')?>).addClass('btn-zv btn-zv-skinned-location').addClass(room.options.add ? '' : 'disabled')))
                    .append(NF.cell(true,{desktop: 6, sm: 12},0,'center').append(btn_con =  NF.button(<?=__j('Umbauen...')?>).addClass('btn-zv btn-zv-skinned-location').addClass(room.options.construct ? '' : 'disabled')))
                );
                $.each(room.options.actions, function(aid,hid) {
                    action_row.append(NF.cell(true,{desktop: 12, sm: 12},0,'center').append(core.snippets.button(
                        hid,
                        function() {popup.trigger('unpop');},
                        'tooltip',
                        function(target) {
                            if (target == 'maker' || target == 'fighter') {
                                titlebar.children('.center').text(game.i18n(target == 'fighter' ? <?=__j('Verteidigen: :room')?> : <?=__j('Items herstellen: :room')?>, {':room': room_identifier}));
                                core.popup.genericBlueprintLoader(target,room.id,popup);
                            } else alert("ERROR: Invalid transition '" + target + "'. Lazy dev needs to implement generic popup content hand-over for this to work!");
                        }
                    )))
                })

                btn_add.click(function() {
                    titlebar.children('.center').text(game.i18n(<?=__j('Ausbauen: :room')?>, {':room': room_identifier}));
                    core.popup.genericBlueprintLoader('builder',room.id,popup);
                });
                btn_con.click(function() {
                    titlebar.children('.center').text(game.i18n(<?=__j('Umbauen: :room')?>, {':room': room_identifier}));
                    core.popup.genericBlueprintLoader('tine',room.id,popup);
                });

                rc++
            })
        });
    }
};