<?php
/**
 * @var array $dates
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Error Logs</h1>

<div class="row">
    <h3>Battle Logs</h3>
    <div class="cell rw-4">
        <input class="form_input" id="blg_bid" text="" type="text" placeholder="Battle ID"/>
    </div>
    <div class="cell rw-4">
        <input class="form_input" id="blg_bstr" text="" type="text" placeholder="Gallery ID"/>
    </div>
    <div class="cell rw-4">
        <button id="blg_go" class="btn">Öffnen</button>
    </div>
</div>

<div class="row" data-dist="0">
    <h2>Heute</h2>
</div>

<div class="row" data-dist="1">
    <h2>Die letzten 3 Tage</h2>
</div>

<div class="row" data-dist="3">
    <h2>Älter als 3 Tage</h2>
</div>

<div class="row" data-dist="14">
    <h2>Älter als 14 Tage</h2>
</div>

<div class="row" data-dist="30">
    <h2>Älter als 30 Tage</h2>
</div>


<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
(function() {

    $('#blg_go').click(function() {
        var bid = $('#blg_bid').val();
        if (!bid) bid = 0;
        var gid = $('#blg_bstr').val();
        if (!gid) gid = 0;

        window.open('admin/files/battle_log/' + (bid + '-' + gid));
    });


    var list = <?=json_encode($dates)?>;
    list.sort();
    list.reverse();

    $.each(list, function(k,v) {
        var raw_date = v.split('-');
        var current = new Date(raw_date[0],raw_date[1] - 1,raw_date[2]);

        var target;
        var distance = (Date.now() - current.getTime()) / 86400000;
        if (distance <= 1)       target = $('[data-dist=0]');
        else if (distance <= 3)  target = $('[data-dist=1]');
        else if (distance <= 14) target = $('[data-dist=14]');
        else                     target = $('[data-dist=30]');

        target.append(
            NF.cell(true,12,0,'pointer').data('h',false).click(function() {
                var alias = $(this);
                if ($(this).children('[data-log]').length == 0)
                    game.network.query('admin/japi/logs/fetch', {id: v}, function(data) {
                        if (data.id != v || !data.log) alert('Error fetching log!');
                        else {
                            var target;
                            $.each(data.log, function(id, entry) {
                                alias.append(
                                    NF.row().attr('data-log',1).append(NF.cell(true,12).append(
                                        target = $('<div />').addClass('flatbox').append($('<h3>').text(id))
                                    ))
                                );

                                $.each(entry, function(sec,txt) {
                                    var pre;
                                    target.append($('<b />').text(sec)).append(pre = $('<div />'));
                                    $.each(txt, function(ln,line) {
                                        pre.append($('<div />').css({'font-family': 'monospace',margin: 3}).text(line));
                                    });
                                });
                            });

                            alias.children('[data-log]').hide().slideToggle(true);
                        }
                    });
                else {
                    $(this).children('[data-log]').slideToggle($(this).data('h'));
                    $(this).data('h', !$(this).data('h'));
                }
            }).data({time: current.getTime(), id: v}).append($('<div />').addClass('flatbox').append($('<b />').text(current.toLocaleDateString())))
        );
    });

    $('[data-dist').each(function() {
        if ($(this).children('div').length == 0) $(this).hide();
    });
})();
// ## JS COMPRESS END ## //
</script>