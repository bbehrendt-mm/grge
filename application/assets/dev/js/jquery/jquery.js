(function(){
    $.objToArray = function(obj, values) {
        return obj ? $.map(obj, function(k,v) {return values ? k : v;}) : [];
    };

    $.fn.softText = function(str) {
        if (!str) return this.text();
        else if (this.text() != str) this.text(str);
    };

    $.fullscreenAllowed = function() {
        return document.fullscreenEnabled || document.webkitFullscreenEnabled ||
            document.mozFullScreenEnabled || document.msFullscreenEnabled;
    };

    $.exitFullscreen = function() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    };

    $.fn.fullscreen = function() {
        if ($(this).length < 1) return;
        var elem = $(this).get(0);
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else alert('Sorry, your browser does not seem to support switching to fullscreen.');
    };


    $.fn.customRadioCheck = function() {

        return this.each(function() {

            var $this = $(this);
            var $span = $('<span/>');

            $span.addClass('custom-'+ ($this.is(':checkbox') ? 'check' : 'radio'));
            $this.is(':checked') && $span.addClass('checked'); // init
            $span.insertAfter($this);

            $this.parent('label').addClass('custom-label')
                .attr('onclick', ''); // Fix clicking label in iOS
            // hide by shifting left
            $this.css({ position: 'absolute', left: '-9999px' });

            // Events
            $this.on({
                change: function() {
                    if ($this.is(':radio')) {
                        $this.parent().siblings('label')
                            .find('.custom-radio').removeClass('checked');
                    }
                    $span.toggleClass('checked', $this.is(':checked'));
                },
                focus: function() { $span.addClass('focus'); },
                blur: function() { $span.removeClass('focus'); }
            });
        });
    };

    var injectCleaner = function(jqFuncName) {
        var backup = jQuery.fn[jqFuncName];
        jQuery.fn[jqFuncName] = function() {
            if (jQuery.fn.qtip) $(this).find('*[data-hasqtip]').qtip('destroy',true);
            return backup.apply(this,arguments);
        };
    };

    $.each(['html','empty','remove'],function(k,v) {injectCleaner(v)});

    //round
    (function() {
        /**
         * Decimal adjustment of a number.
         *
         * @param {String}  type  The type of adjustment.
         * @param {Number}  value The number.
         * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
         * @returns {Number} The adjusted value.
         */
        function decimalAdjust(type, value, exp) {
            // If the exp is undefined or zero...
            if (typeof exp === 'undefined' || +exp === 0) {
                return Math[type](value);
            }
            value = +value;
            exp = +exp;
            // If the value is not a number or the exp is not an integer...
            if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
                return NaN;
            }
            // Shift
            value = value.toString().split('e');
            value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
            // Shift back
            value = value.toString().split('e');
            return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
        }

        // Decimal round
        if (!Math.round10) {
            Math.round10 = function(value, exp) {
                return decimalAdjust('round', value, exp);
            };
        }
        // Decimal floor
        if (!Math.floor10) {
            Math.floor10 = function(value, exp) {
                return decimalAdjust('floor', value, exp);
            };
        }
        // Decimal ceil
        if (!Math.ceil10) {
            Math.ceil10 = function(value, exp) {
                return decimalAdjust('ceil', value, exp);
            };
        }
    })();

    $.fn.qtt = function(d, f) {
        $(this).attr('title','-').qtip(game.render.html.qtip.ingame(d, {
            render: function (event, api) {
                f.call($(this).find('.qtip-content').empty().get(0), event, api);
            }
        }));
    };

    //IE FIXES
    if (window.navigator.userAgent.toUpperCase().indexOf('MSIE') >= 0 || window.navigator.userAgent.toUpperCase().indexOf('TRIDENT') >= 0) {

        console.warn('Internet Explorer or Trident Browser Engine detected. We need to be careful not to scare him with our space age web technology, so let\'s disable some of it. Please consider using a better browser for this game.');

        var old_animate = $.fn.animate;
        $.fn.animate = function(properties) {
            if (properties.transform)
                $(this).css({transform: properties.transform});
            return old_animate.apply(this,arguments);
        };
    }
}());