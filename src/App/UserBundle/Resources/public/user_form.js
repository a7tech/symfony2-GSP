(function($) {
    this.initUserForm = function(ajax_email_url) {
        $(document).delegate("input[id$='_person_autocomplete']", 'change', function() {
            var $person = $(this);
            var personId = $person.parent().find("span[class='ui-helper-hidden-accessible']").text();
            if (personId) {
                var url = ajax_email_url.replace('__personId__', personId);
                $.getJSON(url, function(data) {
                    var $autocomplete = $person.parent().parent().parent().find("input[id$='_email_autocomplete']");
                    var emailCategoryWidget = $autocomplete.data('email-autocomplete-widget');
                    emailCategoryWidget.setChoices(data.choices);
                    $autocomplete.removeAttr('disabled');
                });
            }
        });
    }
})(jQuery);
