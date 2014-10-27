(function($) {
    this.initAddressForm = function(ajax_province_url, ajax_region_url) {

        $("input[type=checkbox][id$='_isMain']").change(function(){
            var $this = $(this);

            if ($this.is(":checked")) {
                $("input[type=checkbox][id$='_isMain']").not($this).removeAttr('checked');
            }
        });

        $('select[id$="_country"]').change(function() {
            var $parent = $(this).parent().parent().parent();
            var $province = $parent.find('select[id$="_province"]'),
                $region = $parent.find('select[id$="_region"]'),
                countryId = $(this).find(":selected").attr('value');

            $.ajax({
                url: ajax_province_url.replace('__countryId__', countryId)
            }).done(function(data) {
                var dataO= data['res'],
                    options = '<option >Choose an option</option>',
                    regionOptions = '<option >Choose an option</option>';

                if(dataO.length > 0) {

                    for (var i = 0; i < dataO.length; i++) {
                        options += '<option value="' + dataO[i]['id'] + '">' + dataO[i]['name'] + '</option>';
                    }

                    $province.html(options);
                    $province.removeAttr('disabled');

                } else {
                    $province.html(options);
                    $province.attr('disabled', true);
                }

                $region.html(regionOptions);
                $region.attr('disabled', true);
            });
        });

        $('select[id$="_province"]').change(function() {
            var $parent = $(this).parent().parent().parent();
            var $region = $parent.find('select[id$="_region"]');
            var provinceId = $(this).find(":selected").attr('value');

            $.ajax({
                url: ajax_region_url.replace('__provinceId__', provinceId)
            }).done(function(data) {
                var dataO= data['res'],
                    options = '<option >Choose an option</option>';

                if(dataO.length > 0) {

                    for (var i = 0; i < dataO.length; i++) {
                        options += '<option value="' + dataO[i]['id'] + '">' + dataO[i]['name'] + '</option>';
                    }

                    $region.html(options);
                    $region.removeAttr('disabled');
                } else {
                    $region.html(options);
                    $region.attr('disabled', true);
                }
            });
        });
    }
})(jQuery);