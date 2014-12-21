(function(){
    $.objToArray = function(obj, values) {
        return obj ? $.map(obj, function(k,v) {return values ? k : v;}) : [];
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
            $(this).find('*[data-hasqtip]').qtip('destroy',true);
            return backup.apply(this,arguments);
        };
    };

    $.each(['html','empty','remove'],function(k,v) {injectCleaner(v)});
}());