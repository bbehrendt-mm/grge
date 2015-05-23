core.popup = {
    spawn: function(dx,dy) {
        var wrapper = $('<div />').addClass('popup-wrapper').appendTo('body');

        var popup = $('<div />').addClass('popup').css({
            height: dy,
            width: dx
        }).on('reposition', function() {
            $(this).css({
                top: ((dy + 60) > window.innerHeight) ? 0 : 60,
                left: $(window).width()/2 - dx/2
            });
        }).trigger('reposition').appendTo(wrapper);

        var z = game.render.html.modal.blend(function() {
            popup.addClass('disabled').css({
                '-webkit-filter': 'blur(5px)',
                'filter': 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: 'scale(1.5)'
            }, 400, 'swing', function() {
                $(this).parent().remove();
            });
        }, true, true);

        wrapper.css('z-index',z+1);

        popup.on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).css({
            opacity: 0,
            transform: 'scale(0.5)',
            'transition': 'filter 0.4s ease, -webkit-filter 0.4s ease',
            '-webkit-filter': 'blur(5px)',
            'filter': 'blur(5px)'
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
        var popup = core.popup.spawn(500,300);

        var frame = $('<div />').addClass('row').appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 0,
                bottom: 36,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = $('<div />').addClass('row').css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 36,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn').text(<?=__j('Abbrechen');?>).appendTo($('<div />').addClass('cell rw-4').appendTo(bottom)).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn').text(<?=__j('Anwenden');?>).appendTo($('<div />').addClass('cell ro-4 rw-4').appendTo(bottom)).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters, class_cell;
        frame.append(
            $('<div />').addClass('row').append(
                $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text(<?=__j('Status')?>)).append(typefilters = $('<div />').addClass('row')))
            ).append(
                class_cell = $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text(<?=__j('Kategorie')?>)).append(classfilters = $('<div />').addClass('row')))
            )
        );

        $.each(typeFilterData, function(id, obj) {
            if (id == 'categories') return;
            var chk;
            typefilters.append(
                $('<div />').addClass('cell rw-6').append(
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
                $('<div />').addClass('cell rw-4').append(
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
        if (!popup) popup = core.popup.spawn(700,450);

        if (!frame || !filters || !close) {
            popup.empty();

            frame = $('<div />').addClass('row').appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: 24,
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            ).data('type-filters', {
                    'impossible': {active: true, name: <?=__j('Unmögliche Projekte')?>},
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

            $('<div />').addClass('row').append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text('Angezeigte Projekte filtern...').click(function() {
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

                targets.append($('<div />').addClass('cell padded rw-4').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
                    var prev_scroll = $('.popup').find('>*:first-child').scrollTop();
                    popup.addClass('disabled');
                    core.command('location/' + type, {build: k}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        frame.trigger('filter');
                        $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        popup.off('unpop').on('unpop', function() {
                            setTimeout(function() {
                                core.command();
                            }, 100);
                        })
                    }, true)
                })));
            });

            var tf = frame.data('type-filters');
            $.each(categories, function(name, t) {
                tf.categories[name] = typeof tf.categories[name] !== "undefined" ? tf.categories[name] : true;
            });
            frame.data('type-filters', tf);
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

    map: function() {
        var dx = 800, dy = 602, border = 30, iconsize = 22;
        var gx = Math.floor(dx/iconsize), gy = Math.floor(dy/iconsize);
        var bsize = 64;
        var grid = {};

        for (var i = 0; i < gx; i++) {
            grid[i*iconsize] = {};
            for (var j = 0; j < gy; j++)
                grid[i*iconsize][j*iconsize] = false;
        }

        var popup = core.popup.spawn(dx+2, dy + bsize);

        popup.append(core.snippets.wait());

        core.command('map/data', {}, true, function(data) {
            popup.empty();

            if ($.objToArray(data.locations).length < 2) {
                popup.append($('<div />').addClass('center').css('margin-top', 200).text(<?=__j('Die Karte steht derzeit nicht zur Verfügung!')?>));
                popup.append($('<div />').addClass('center').append($('<div />').addClass('btn small').text(<?=__j('Schließen')?>).click(function() {
                    popup.trigger('unpop');
                })));
                return;
            }

            var canvas = $('<canvas />').attr({
                'width':dx,
                'height':dy
            }).addClass('map').appendTo(popup);
            var ctx = canvas.get(0).getContext("2d");

            var bottom = $('<div />').addClass('row').css({
                height: bsize,
                'margin-top': dy
            }).appendTo(popup);

            var infopanel = $('<div />').hide().addClass('map panel').appendTo(
                $('<div />').addClass('cell rw-6 ro-3').css({
                    position: 'relative',
                    height: '100%'
                }).appendTo(bottom)
            );

            var sdmin = {}, sdmax = {};
            var init = true;

            var logf = function(k,v) {
                if (init) {
                    sdmax.x = sdmin.x = v.x;
                    sdmax.y = sdmin.y = v.y;
                    init = false;
                } else {
                    sdmax.x = Math.max(sdmax.x, v.x);
                    sdmax.y = Math.max(sdmax.y, v.y);
                    sdmin.x = Math.min(sdmin.x, v.x);
                    sdmin.y = Math.min(sdmin.y, v.y);
                }
            };

            var scalef = function(k,v) {
                v.x = border + (v.x - sdmin.x)/(sdmax.x - sdmin.x) * (dx-border*2);
                v.y = border + (v.y - sdmin.y)/(sdmax.y - sdmin.y) * (dy-border*2);
            };

            var p = function(v,dx,dy) {
                return {x: v.x-dx/2, y: v.y-dy/2}
            };

            var gridify = function(jqo) {
                if (jqo.data('grid'))
                    return false;
                else {
                    var px = jqo.data('x');
                    var py = jqo.data('y');

                    //Closest grid
                    var d = false;
                    var current = false;
                    $.each(grid, function(sx,line) {
                        $.each(line, function(sy,f) {
                            var x = parseInt(sx); var y = parseInt(sy);
                            var tmp;
                            if (!f && (((tmp = Math.pow(px-x,2)+Math.pow(py-y,2)) < d) || d === false )) {
                                d = tmp;
                                current = [x,y];
                            }
                        })
                    });

                    if (current) {
                        jqo.css({
                            left: current[0],
                            top: current[1]
                        });
                        grid[current[0]][current[1]] = true;
                    }

                    jqo.data('grid',true);
                    return true;
                }
            };

            var draw = function(mark, color) {
                if (!mark)  mark = [];
                if (!color) color = '#39ACE5';

                ctx.clearRect(0, 0, dx, dy);

                var subdraw = function(x0,x1,y0,y1,color, hidden) {

                    var line = Math.min(4,Math.max(2,Math.floor(Math.sqrt(Math.pow(x0-x1,2)+Math.pow(y0-y1,2))/15)));

                    ctx.save();
                    ctx.lineWidth = line;
                    ctx.strokeStyle = color;
                    ctx.fillStyle = color;
                    ctx.shadowColor = color;
                    ctx.shadowBlur = 15;
                    ctx.shadowOffsetX = 0;
                    ctx.shadowOffsetY = 0;

                    ctx.beginPath();
                    ctx.moveTo(x0,y0);
                    ctx.lineTo(x1,y1);
                    ctx.stroke();

                    ctx.lineWidth = 0;

                    ctx.arc(x0,y0, line/2, 0, 2 * Math.PI, false);
                    ctx.fill();
                    ctx.arc(x1,y1, line/2, 0, 2 * Math.PI, false);
                    ctx.fill();
                    ctx.restore();
                };

                $.each(data.network, function(start, nodes) {
                    $.each(nodes, function(k, end) {
                        var fstp = mark.indexOf(start); var lstp = mark.indexOf(end);

                        if (fstp >= 0 && lstp >= 0 && fstp != lstp && Math.abs(fstp-lstp) == 1)
                            return;

                        if (data.nodes[start].active && data.nodes[end].active)
                            subdraw(data.nodes[start].x,data.nodes[end].x,data.nodes[start].y,data.nodes[end].y,'#888888');
                    });
                });

                $.each(data.network, function(start, nodes) {
                    $.each(nodes, function(k, end) {

                        var fstp = mark.indexOf(start); var lstp = mark.indexOf(end);

                        if (!(fstp >= 0 && lstp >= 0 && fstp != lstp && Math.abs(fstp-lstp) == 1))
                            return;

                        subdraw(data.nodes[start].x,data.nodes[end].x,data.nodes[start].y,data.nodes[end].y, color);
                    });
                });
            };

            $.each(data.locations, logf);
            $.each(data.nodes, logf);

            var dif = Math.abs(Math.abs(sdmax.x - sdmin.x) - Math.abs(sdmax.y - sdmin.y));
            if (Math.abs(sdmax.x - sdmin.x) > Math.abs(sdmax.y - sdmin.y)) {
                sdmin.y -= dif/2;
                sdmax.y += dif/2;
            } else {
                sdmin.x -= dif/2;
                sdmax.x += dif/2;
            }

            $.each(data.locations, scalef);
            $.each(data.nodes, scalef);

            $.each(data.locations, function(k,v) {

                $.each(v.nodes, function(nnum, nk) {
                    data.nodes[nk].active = true;
                });

                var pos = p(v,iconsize,iconsize);
                popup.append(
                    $('<div />').addClass(k == data.current ? 'map location active' : (!(data.read_only && !v.skip_ro) && v.energy <= data.radius ? 'map location' : 'map location unreachable')).attr({
                        'data-location':k,
                        'data-x': pos.x,
                        'data-y': pos.y
                    }).css({
                        top: pos.y,
                        left: pos.x
                    }).append(
                        $('<img />').attr('src','media/icons/places/' + v.icon)
                    ).mouseenter(function() {
                        draw($.objToArray(v.nodes,true), !(data.read_only && !v.skip_ro) && v.energy <= data.radius ? '#39ACE5' : '#E3573B');

                        $(this).siblings().each(function() {
                            var id = $(this).data('location');
                            if (id != v.current && id != k && ($.objToArray(v.route, true).indexOf(id) >= 0))
                                $(this).addClass(!(data.read_only && !v.skip_ro) && v.energy <= data.radius ? 'travel' : 'untravel');
                        });

                        infopanel.empty().stop().fadeIn(100).append(
                            $('<h3 />').text(v.name)
                        ).append(
                            $('<div />').addClass('row center').append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/distance.gif' )
                                ).append(
                                    $('<span />').text(v.distance)
                                )
                            ).append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/status_energy.gif' )
                                ).append(
                                    $('<span />').text(v.energy)
                                )
                            ).append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/zombie.gif' )
                                ).append(
                                    $('<span />').text(v.zombies)
                                )
                            ).append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/status_weight.gif' )
                                ).append(
                                    $('<span />').text(v.weight === null ? 0 : v.weight )
                                )
                            )
                        )
                    }).mouseleave(function() {
                        draw();
                        infopanel.stop().fadeOut(100);
                        $(this).siblings('.travel, .untravel').removeClass('travel untravel');
                    }).click(function() {
                        if ((data.read_only && !v.skip_ro) || v.energy > data.radius || k == data.current) return;

                        if (v.zombies && !confirm(game.i18n(<?=__j('Dieser Ort wird von :zombies Zombies belagert. Wenn du diesen Ort betrittst, wirst du kämpfen müssen. Weiter?')?>, {':zombies': v.zombies}))) return;

                        var route_zombies = [];
                        $.each(v.route, function(rkey, rval) {
                            if (rval == v.id || rval == data.current) return;
                            if (data.locations[rval].zombies > 0)
                                route_zombies.push(data.locations[rval].name);
                        });
                        if (route_zombies.length && !confirm(game.i18n(<?=__j('Auf dem Weg zu diesem Ort befinden sich Zombies (:locations). Du wirst gegen sie kämpfen müssen, wenn du dorthin möchtest. Weiter?')?>,{':locations': route_zombies.join(', ')}))) return;

                        if (core.last.players) {
                            var esc_popup = core.popup.spawn(400);

                            var title;
                            esc_popup.append($('<h2 />').addClass('center').text(v.name));

                            esc_popup.append(
                                $('<div />').addClass('row').append(title = $('<div />').addClass('cell rw-12 padded').text(<?=__j('Wenn du dich alleine fürchtest, kannst du andere Spieler bitten, dich zu begleiten. Oder noch besser, schick sie am besten direkt vor, nicht dass noch jemand (z.B. du) verletzt wird!')?>))
                            );

                            var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                            if (core.last.players.others)
                                $.each(core.last.players.others, function(id, player) {
                                    if (player.escort)
                                        check_row.append($('<div />').addClass('cell rw-6 padded').append(
                                            $('<label />').text(player.name).prepend($('<input />').attr('type','checkbox').attr('data-id', player.id))
                                        ))
                                });


                            if (check_row.children().length) {
                                var bhav;

                                check_row
                                    .prepend($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Wer soll alles mitkommen?')?>)))
                                    .append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Und wie siehts mit dir aus?')?>)))
                                    .append($('<div />').addClass('cell rw-12 padded').append(
                                        bhav = $('<select />')
                                            .append($('<option />').val('2').text(<?=__j('Mitgehen und helfen')?>))
                                            .append($('<option />').val('1').text(<?=__j('Nur mitgehen')?>))
                                            .append($('<option />').val('0').text(<?=__j('Die Stellung halten')?>))
                                            .val('1')
                                    ));

                                bhav.selectric();
                                check_row.find(':checkbox').customRadioCheck();

                            } else title.text(<?=__j('Bist du sicher, dass du diesen Ort betreten möchtest? Er ist weit weg, und riecht auch bestimmt nicht sehr gut...')?>);

                            esc_popup.append($('<div />').addClass('row')
                                .append($('<div />').addClass('cell rw-8 padded').append(
                                    $('<div />').addClass('btn').text(<?=__j('Los gehts!')?>).click(function() {

                                        var cfg = {to: k, follow: 1};
                                        if (check_row.children().length) {
                                            cfg.follow = parseInt(bhav.val()) > 0 ? 1 : 0;
                                            cfg.support = parseInt(bhav.val()) == 2 ? 1 : 0;
                                            cfg.co = [];
                                            $.each(check_row.find(':checkbox:checked'), function() {
                                                cfg.co.push($(this).attr('data-id'))
                                            })
                                        }

                                        esc_popup.trigger('unpop');
                                        popup.addClass('disabled');
                                        core.command('map/go', cfg, true, function(data) {
                                            popup.removeClass('disabled');
                                            if (data.success) {
                                                popup.trigger('unpop');
                                                core.command();
                                            }
                                        });
                                    })))
                                .append($('<div />').addClass('cell rw-4 padded').append(
                                    $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(function() {
                                        esc_popup.trigger('unpop');
                                    })))
                            );
                        } else {
                            popup.addClass('disabled');
                            core.command('map/go', {to: k, follow: 1}, true, function(data) {
                                popup.removeClass('disabled');
                                if (data.success) {
                                    popup.trigger('unpop');
                                    core.command();
                                }
                            });
                        }


                    })
                )
            });

            popup.find('.map.location').each(function() {
                gridify($(this));
            });

            draw();
        });
    }
};