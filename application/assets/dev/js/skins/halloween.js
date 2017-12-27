window.addEventListener('load',function(){

    window.setTimeout(function() {

        var animate = function(id) {

            if (game.s.quality() < 2)
                return;

            switch (id) {
                case 1:
                    startCSS = {left: -1024, top: 0};
                    stopCSS = {left: 0};
                    duration = 24000;
                    break;
                case 2:
                    startCSS = {left: -1280, top: 100};
                    stopCSS = {left: -256};
                    duration = 28100;
                    break;
                case 3:default:
                    startCSS = {left: -512, top: 200};
                    stopCSS = {left: -1536};
                    duration = 36040;
                break;
            }

            $('#halloweenFogSFX' + id)
                .css(startCSS)
                .animate(stopCSS, duration, 'linear', function() {
                    animate(id);
                })

        };

        var basicCss = {
            'background-image': 'url(media/img/fog.png)',
            'background-position': 'bottom',
            'background-repeat': 'repeat-x',
            opacity: 0.15,
            'pointer-events': 'none',
            position: 'fixed',
            height: '100%',
            top: 0,
            left: '-100%',
            width: 10240
        };

        if (game.s.quality() >= 3) {
            $('body').append(
                $('<div class="sfx" id="halloweenFogSFX1" />').css(basicCss)
            ).append(
                $('<div class="sfx" id="halloweenFogSFX2" />').css(basicCss)
            ).append(
                $('<div class="sfx" id="halloweenFogSFX3" />').css(basicCss)
            );

            animate(1);
            animate(2);
            animate(3);
        } else if (game.s.quality() > 1) jQuery('body').append(jQuery('<div id="halloweenFogSFX1" />').css(basicCss).css('opacity', 0.45));

    }, 1000);

});