(function($) {
    this.initSkillForm = function(ajax_speciality_url, ajax_skill_category_url) {
        $(document).delegate("select[id$='_sector']", 'change', function() {
            var $sector = $(this);
            var sectorId = $sector.val();
            if (sectorId) {
                var url = ajax_speciality_url.replace('__sectorId__', sectorId);
                $.getJSON(url, function(data) {
                    var options = '<option value="">Choose a speciality</option>';
                    options += createOptions(data.res);
                    var $speciality = $sector.parents('.collection-row').find("select[id$='_speciality']");
                    $speciality.html(options);
                    $speciality.removeAttr('disabled');
                });
            }
        });
        $(document).delegate("select[id$='_speciality']", 'change', function() {
            var $speciality = $(this);
            var specialityId = $speciality.val();
            if (specialityId) {
                var url = ajax_skill_category_url.replace('__specialityId__', specialityId);
                $.getJSON(url, function(data) {
                    var $autocomplete = $speciality.parents('.collection-row').find("input[id$='_autocomplete']");
                    var skillCategoryWidget = $autocomplete.data('skill-category-widget');
                    skillCategoryWidget.setChoices(data.choices);
                    $autocomplete.removeAttr('disabled');
                });
            }
        });
    };

    function createOptions(choices) {
        var options = '';
        choices.forEach(function(item) {
            options += '<option value="'+item.id+'">'+item.name+'</option>';
        });
        return options;
    }
})(jQuery);