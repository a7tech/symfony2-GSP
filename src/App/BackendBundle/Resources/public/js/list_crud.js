(function($) {
    this.initListCrud = function(inputIdPrefix) {
        $('input[id^="'+inputIdPrefix+'"]').change(function(e) {
            var $input = $(e.target);
            var checkboxId = $input.attr('id');

            if ($input.is(':checked')) {
                var rowId = $input.parents('tr').data('id');

                $('input[id!="'+checkboxId+'"][id^="'+inputIdPrefix+'"]').each(function() {
                    $(this).attr('disabled', true);
                });

                $('li[class*="crud"]').each(function(){
                    var href = $(this).attr('id')+'/';
                    $(this).removeClass('disabled');
                    $(this).children('a').attr('href', href+rowId);
                })
            }
            else {
                $('input[id!="'+checkboxId+'"][id^="'+inputIdPrefix+'"]').each(function() {
                    $(this).removeAttr('disabled');
                });

                $('li[class*="crud"]').each(function(){
                    var href = $(this).attr('id')+'/';
                    $(this).addClass('disabled');
                    $(this).children('a').removeAttr('href');
                })
            }
        });
    }
})(jQuery);
