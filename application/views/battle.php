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
<body>
    <canvas width="640" height="400" id="output"></canvas>

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

                        if (status == 'abort')
                            return;
                        if (status == 'timeout')
                            console.error({error: 'GRGE-0001-0000', name: 'E_CLIENT_CONNECTION_TIMEOUT', message: 'Connection timed out.'});
                        else console.error({error: 'GRGE-0002-0000', name: 'E_SERVER_ERROR', message: 'Unexpected error while processing the request.'});
                    });
                };

                var receiver = function(data) {
                    battle = new Battle('output', data.video);
                    battle.load();
                    battle.proceed();
                };

                $.getScript('../web/battle/?l=' + 'de', function() {
                    loader();
                }).fail(function( jqxhr, settings, exception ) {
                    console.error(exception);
                });
            });
        })();
    </script>
</body>
</html>