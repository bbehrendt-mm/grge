core.popup = {
    spawn: function(dx,dy) {
        var popup = $('<div />').addClass('popup').css({
            height: dy,
            width: dx,
            top: 60,
            left: $(window).width()/2 - dx/2
        });

        var z = game.render.html.modal.blend(function() {
            popup.addClass('disabled').css({
                '-webkit-filter': 'blur(5px)',
                'filter': 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: 'scale(1.5)'
            }, 400, 'swing', function() {
                $(this).remove();
            });
        }, true, true);

        popup.css('z-index',z+1).on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).appendTo('body').css({
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
                bottom: 26,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = $('<div />').addClass('right').css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 26,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn small').text(<?=__j('Abbrechen');?>).appendTo(bottom).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn small').text(<?=__j('Anwenden');?>).appendTo(bottom).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters;
        frame.append(
            $('<div />').addClass('row').append(
                typefilters = $('<div />').addClass('cell rw-6')
            ).append(
                classfilters = $('<div />').addClass('cell rw-6')
            )
        );

        $.each(typeFilterData, function(id, obj) {
            var chk;
            typefilters.append(
                $('<div />').append(
                    $('<label />').text(obj.name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('tid', id).prop('checked', obj.active).click(function() {
                            typeFilterData[id].active = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
        })
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
                    'done': {active: true, name: <?=__j('Abgeschlossene Projekte')?>}
            }).on('filter', function() {
                    var typefilters = $(this).data('type-filters');
                    if (typefilters.impossible.active)  $(this).find('.blueprint.red').parent().show();
                    else                                $(this).find('.blueprint.red').parent().hide();

                    if (typefilters.locked.active)      $(this).find('.blueprint.plain').parent().show();
                    else                                $(this).find('.blueprint.plain').parent().hide();

                    if (typefilters.possible.active)    $(this).find('.blueprint.green').parent().show();
                    else                                $(this).find('.blueprint.green').parent().hide();

                    if (typefilters.ready.active)       $(this).find('.blueprint.green.active').parent().show();
                    else                                $(this).find('.blueprint.green.active').parent().hide();

                    if (typefilters.done.active)        $(this).find('.blueprint.blue').parent().show();
                    else                                $(this).find('.blueprint.blue').parent().hide();
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
            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;

                frame.append($('<div />').addClass('cell padded rw-4').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
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
            })
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
        var bsize = 120;
        var grid = {};

        for (var i = 0; i < gx; i++) {
            grid[i*iconsize] = {};
            for (var j = 0; j < gy; j++)
                grid[i*iconsize][j*iconsize] = false;
        }

        var popup = core.popup.spawn(dx+2,dy+2+bsize);

        popup.append(core.snippets.wait());

        core.command('map/data', {}, true, function(data) {
            popup.empty();

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
                $('<div />').addClass('cell rw-4').css({
                    position: 'relative',
                    height: '100%'
                }).appendTo(bottom)
            );
            var doorways = $('<div />').addClass('map panel').appendTo(
                $('<div />').addClass('cell rw-4').css({
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
                    $('<div />').addClass(k == data.current ? 'map location active' : (!data.read_only && v.energy <= data.radius ? 'map location' : 'map location unreachable')).attr({
                        'data-location':k,
                        'data-x': pos.x,
                        'data-y': pos.y
                    }).css({
                        top: pos.y,
                        left: pos.x
                    }).append(
                        $('<img />').attr('src','media/icons/places/' + v.icon)
                    ).mouseenter(function() {
                        draw($.objToArray(v.nodes,true), !data.read_only && v.energy <= data.radius ? '#39ACE5' : '#E3573B');

                        $(this).siblings().each(function() {
                            var id = $(this).data('location');
                            if (id != v.current && id != k && ($.objToArray(v.route, true).indexOf(id) >= 0))
                                $(this).addClass(!data.read_only && v.energy <= data.radius ? 'travel' : 'untravel');
                        });

                        infopanel.empty().stop().fadeIn(200).append(
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
                        infopanel.stop().fadeOut(600);
                        $(this).siblings('.travel, .untravel').removeClass('travel untravel');
                    }).click(function() {
                        if (data.read_only || v.energy > data.radius || k == data.current) return;

                        popup.addClass('disabled');
                        core.command('map/go', {to: k, follow: 1}, true, function(data) {
                            popup.removeClass('disabled');
                            if (data.success) {
                                popup.trigger('unpop');
                                core.command();
                            }
                        });
                    })
                )
            });

            doorways.append(
                $('<h3 />').text(<?=__j('Andere Orte');?>)
            );
            $.each(data.doorways, function(k,v) {
                doorways.append(
                    $('<div />').addClass('btn small block').text(v.name + ' (' + v.location + ')').click(function() {
                        popup.addClass('disabled');
                        core.command('map/go', {to: k, follow: 1}, true, function(data) {
                            popup.removeClass('disabled');
                            if (data.success) {
                                popup.trigger('unpop');
                                core.command();
                            }
                        });
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