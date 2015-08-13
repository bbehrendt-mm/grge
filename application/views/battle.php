<?php
    /**
     * @var string $path
     * @var int|null $pid
     * @var int $bid
     */
if (!isset($path)) $path = '';
?>
<html>
<head>
    <!-- Meta -->
    <meta content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="de">
    <meta name="robots" content="noindex,nofollow" />
    <meta name="author" content="Benjamin 'Brainbox' Behrendt" />

    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css" />
    <style>
        #play {background: rgb(169,3,41); background: linear-gradient(to bottom, rgba(169,3,41,1) 0%,rgba(143,2,34,1) 44%,rgba(109,0,25,1) 100%); border: 3px solid rgb(169,3,41); border-radius: 10px; text-align: center; color: white; cursor: pointer}
        #play:hover {background: rgb(202,4,50); background: linear-gradient(to bottom, rgba(202,4,50,1) 0%,rgba(180,2,44,1) 44%,rgba(150,0,35,1) 100%); border: 3px solid rgb(202,4,50);}
    </style>

    <script type="application/javascript" src="../js/jquery.min.js" ></script>
    <script type="application/javascript" src="../js/easeljs-0.8.0.min.js" ></script>
    <script type="application/javascript" src="../js/tweenjs-0.6.0.min.js" ></script>
</head>
<body style="overflow: hidden; padding: 0; margin: 0; background: black">

    <div>
        <img style="position: absolute; left: 120px; top: 64px;" src="../media/icons/battle/logo.png" />
        <div style="position: absolute; left: 0; top: 230px; width: 600px; text-align: center">
            <img id="loading" src="../media/icons/battle/loading.gif" />
        </div>

        <div id="error" style="display: none; position: absolute; left: 20px; top: 230px; width: 588px; text-align: center; font-size: 12px; background: white; border: 2px solid #aaaaaa; box-shadow: 0 0 5px red; padding: 4px; font-family: monospace;"></div>

        <div id="play" style="display: none; position: absolute; z-index: 2; top: 225px; width: 100px; padding: 10px; left: 237px;" >
            <i class="fa fa-play fa-3x"></i>
        </div>
    </div>


    <canvas style="position: absolute; z-index: 1;" width="640" height="400" id="output"></canvas>

    <script type="text/javascript">
        (function() {
            window.addEventListener('load',function(){

                var battle;

                var loader = function() {
                    $.ajax('<?=$path?>japi/embed/battle', {
                        cache: false,
                        type: 'POST',
                        data: {v: <?=$bid?>},
                        timeout: 45000
                    }).done(function(data) {
                        receiver(data);
                    }).fail(function(obj, status) {
                        if (obj && obj.responseText && 0 < (e = obj.responseText.search('<!-- ### GRG CORE INLINE RENDERING EXCEPTION: ERROR PAGE BEYOND THIS LINE ### -->'))) {
                            var d = $(obj.responseText.slice(e).replace(/<(\/{0,1})(html|head|body)(.*?)>/g, '<$1var$2$3>'));
                            jQuery('head').html(d.find('varhead').html());
                            jQuery('body').html(d.find('varbody').html());
                            eval(d.find('varbody').attr('onload'));
                            return;
                        }
                        var error = $('#error').show();

                        if (status == 'abort')
                            error.text('Download aborted by client.');
                        if (status == 'timeout')
                            error.text('Connection timeout.');
                        else error.text('Unexpected server error.');
                    });
                };

                var receiver = function(data) {
                    if (!data.video)
                        return $('#error').show().text('Unable to obtain video file.');

                    battle = new Battle('output', data.video);
                    battle.load();
                    battle.proceed();
                };

                $('#loading').hide();
                $('#play').show().click(function() {
                    $(this).hide();
                    $('#loading').show();
                    $.getScript('../web/battle/?l=' + 'de', function() {
                        loader();
                    }).fail(function( jqxhr, settings, exception ) {
                        $('#error').show().text('Compiler error: ' + exception.message);
                        console.error(exception);
                    });
                });
            });
        })();
    </script>
</body>
</html>