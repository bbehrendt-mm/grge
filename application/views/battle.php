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

    <script type="application/javascript" src="../js/jquery.min.js" ></script>
    <script type="application/javascript" src="../js/easeljs-0.8.0.min.js" ></script>
    <script type="application/javascript" src="../js/tweenjs-0.6.0.min.js" ></script>
</head>
<body style="overflow: hidden; padding: 0; margin: 0; background: black">

    <div>
        <img style="position: absolute; left: 120px; top: 64px;" src="../media/icons/battle/logo.png" />
        <div style="position: absolute; left: 0; top: 230px; width: 600px; text-align: center">
            <img src="../media/icons/battle/loading.gif" />
        </div>

        <div id="error" style="display: none; position: absolute; left: 20px; top: 230px; width: 588px; text-align: center; font-size: 12px; background: white; border: 2px solid #aaaaaa; box-shadow: 0 0 5px red; padding: 4px; font-family: monospace;"></div>
    </div>


    <canvas style="position: absolute;" width="640" height="400" id="output"></canvas>

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

                $.getScript('../web/battle/?l=' + 'de', function() {
                    loader();
                }).fail(function( jqxhr, settings, exception ) {
                    $('#error').show().text('Compiler error: ' + exception.message);
                    console.error(exception);
                });
            });
        })();
    </script>
</body>
</html>