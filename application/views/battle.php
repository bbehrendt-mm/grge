<?php
    /**
     * @var string $path
     * @var int|null $pid
     * @var int $bid
     * @var string $lang
     * @var string $url
     */
if (!isset($path)) $path = '';
?>
<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
    <!-- Meta -->
    <meta content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="de">
    <meta name="robots" content="noindex,nofollow" />
    <meta name="author" content="Benjamin 'Brainbox' Behrendt" />

    <title>ZombVival Battle System</title>

    <link rel="stylesheet" type="text/css" href="<?=$url?>css/font-awesome.min.css" />
    <style>
        #play, #replay {background: rgb(169,3,41); background: linear-gradient(to bottom, rgba(169,3,41,1) 0%,rgba(143,2,34,1) 44%,rgba(109,0,25,1) 100%); border: 3px solid rgb(169,3,41); border-radius: 10px; text-align: center; color: white; cursor: pointer}
        #play:hover, #replay:hover {background: rgb(202,4,50); background: linear-gradient(to bottom, rgba(202,4,50,1) 0%,rgba(180,2,44,1) 44%,rgba(150,0,35,1) 100%); border: 3px solid rgb(202,4,50);}

        #controls {font-size: 0}
        #controls > div {cursor: pointer; font-size: 15px; color: white; height: 18px; width: 32px; padding: 2px; margin: 0; display: inline-block; text-align: center; background: #14171A; border-top: 1px solid #252C33}
        #controls > div:hover {background: #2b323a; text-shadow: 0 0 2px rgba(255,255,255,0.8);}
        #controls > div:last-child {border-right: 1px solid #252C33; border-top-right-radius: 8px}
    </style>

    <script type="application/javascript" src="<?=$url?>js/jquery.min.js" ></script>
    <script type="application/javascript" src="<?=$url?>js/jquery.ext.min.js" ></script>
    <script type="application/javascript" src="<?=$url?>js/createjs.min.js" ></script>
</head>
<body style="overflow: hidden; padding: 0; margin: 0; background: black">

    <div id="preview">
        <div id="prv_big">
            <div style="margin-top: 64px; text-align: center">
                <img src="<?=$url?>media/icons/battle/logo.png" alt="Logo"/>
            </div>
            <div style="text-align: center">
                <img id="loading" src="<?=$url?>media/icons/battle/loading.gif" alt="Loading"/>
            </div>
            <div style="text-align: center">
                <div style="display: inline-block; width: 100px;">
                    <div id="play" style="display: none; text-align: center; padding: 5px;" >
                        <i class="fa fa-play fa-3x"></i>
                    </div>
                </div>

            </div>



            <div id="error" style="display: none; text-align: center; font-size: 12px; background: white; border: 2px solid #aaaaaa; box-shadow: 0 0 5px red; padding: 4px; font-family: monospace; margin: 6px;"></div>


        </div>
    </div>

    <div id="output_container" style="z-index: 1; width: 640px; height: 400px; position: relative;">
        <canvas style="position: absolute; width: 100%; height: 100%" width="640" height="400" id="output"></canvas>
        <div id="controls" data-ready="0" style="display: none; position: absolute; bottom: 0; left: 0; z-index: 2">
            <div id="c_replay"><i class="fa fa-repeat"></i></div>
            <div id="c_fullscreen"><i class="fa fa-expand"></i></div>
            <div id="c_nofullscreen"><i class="fa fa-compress"></i></div>
            <div id="c_pause" data-pause="1"><i class="fa fa-pause"></i></div>
        </div>
        <div id="finish" style=" display: none; position: absolute; top: 0; left: 0; height: 400px; width: 640px; background: rgba(0,0,0,0.5)">
            <div id="replay" style="position: absolute; top: 150px; width: 100px; padding: 10px; left: 237px;" >
                <i class="fa fa-repeat fa-3x"></i>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        (function() {
            var lang = JSON.parse(localStorage.getItem('grge.settings.language'));
            var global_scale = 1;
            if (!lang) lang = '<?=$lang?>';

            window.addEventListener('load',function(){
                var battle;
                var controls = $('#controls');

                $('#output_container').on('mousemove', function() {
                    if (controls.attr('data-ready') === 1 && !controls.is(':visible'))
                        controls.stop(true,true).fadeIn();
                }).on('mouseleave', function() {
                    if (controls.is(':visible'))
                        controls.stop(true,true).fadeOut();
                });

                $(window).resize(function() {
                    var h = $(window).height(); var w = $(window ).width();
                    var scale = Math.min(h/400,w/640);

                    $('#output_container')
                        .css({height: 400 * scale, width: 640 * scale, left: (w - 640 * scale)/2, top: (h - 400 * scale)/2});

                    $('#finish').css({height: 400 * scale, width: 640 * scale});
                    var rpl = $('#replay');
                    rpl.css({top: (400 * scale)/2 - rpl.height()/2, left: (640 * scale)/2 - rpl.width()/2});

                    global_scale = scale;
                    if (battle) battle.rescale(400 * scale,640 * scale,scale);
                }).resize();

                var loader = function() {
                    $.ajax('<?=$url?>japi/embed/battle', {
                        cache: false,
                        type: 'POST',
                        data: {v: <?=$bid?>, g: <?=$pid ?? 0?>},
                        headers: {'X-Request-Lang' : lang},
                        timeout: 45000
                    }).done(function(data) {
                        $('#loading').hide();
                        receiver(data);
                    }).fail(function(obj, status) {
                        var e;
                        if (obj && obj.responseText && 0 < (e = obj.responseText.search('<!-- ### GRG CORE INLINE RENDERING EXCEPTION: ERROR PAGE BEYOND THIS LINE ### -->'))) {
                            var d = $(obj.responseText.slice(e).replace(/<(\/?)(html|head|body)(.*?)>/g, '<$1var$2$3>'));
                            $('head').html(d.find('varhead').html());
                            $('body').html(d.find('varbody').html());
                            eval(d.find('varbody').attr('onload'));
                            return;
                        }
                        var error = $('#error').show();

                        if (status === 'abort')
                            error.text('Download aborted by client.');
                        if (status === 'timeout')
                            error.text('Connection timeout.');
                        else error.text('Unexpected server error.');
                    });
                };

                var receiver = function(data) {
                    if (!data.video)
                        return $('#error').show().text('Unable to obtain video file.');

                    battle = new Battle('output', data.video);

                    $('#c_pause').click(function() {
                        if ($(this).attr('data-pause') === 1) {
                            $(this).attr('data-pause', 0).find('i').removeClass('fa-pause').addClass('fa-play');
                            battle.pause();
                        } else {
                            $(this).attr('data-pause', 1).find('i').removeClass('fa-play').addClass('fa-pause');
                            battle.unpause();
                        }
                    });
                    $('#c_replay').click(function() {
                        battle.reset();
                    });
                    $('#c_fullscreen').click(function() {
                        $('body').fullscreen();
                        $(this).hide();
                        $('#c_nofullscreen').show();
                    }).toggle($.fullscreenAllowed());
                    $('#c_nofullscreen').click(function() {
                        $.exitFullscreen();
                        $(this).hide();
                        $('#c_fullscreen').show();
                    }).hide();
                    $('#replay').click(function() {
                        $('#finish').fadeOut();
                        battle.reset();
                    });

                    battle
                        .on('start', function() {controls.attr('data-ready', 1);})
                        .on('finish', function() {
                            controls.attr('data-ready', 0);
                            if (controls.is(':visible')) controls.fadeOut();

                            $('#finish').fadeIn();
                        });

                    battle.load();
                    battle.begin();

                    $(window).resize();

                    $('#preview').hide();
                };

                $('#loading').hide();
                $('#play').show().click(function() {
                    $(this).hide();
                    $('#loading').show();
                    $.getScript('<?=$url?>web/battle/?l=' + lang, function() {
                        loader();
                    }).fail(function( jqxhr, settings, exception ) {
                        $('#loading').hide();
                        $('#error').show().text('Compiler error: ' + exception.message);
                        console.error(exception);
                    });
                });
            });
        })();
    </script>
</body>
</html>