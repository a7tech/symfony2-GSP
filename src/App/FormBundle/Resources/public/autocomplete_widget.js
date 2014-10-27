(function($, _) {
    var AutocompleteWidget = this.AutocompleteWidget || {};
    this.AutocompleteWidget = AutocompleteWidget;

    AutocompleteWidget.class = function(form_vars) {
        var $input = form_vars.input;
        var choice_list = form_vars.choices;
        var choices = _.values(form_vars.choices);
        var value = form_vars.value;
        var addNew = form_vars.addNew;

        var $hidden = $('<input type="hidden" name="' + form_vars.full_name + '" value="' + form_vars.value + '" />');
        $input.parent().append($hidden);
        $input.parent().addClass('autocomplete-wrapper');

        if (addNew === true) {
            var $autocreate = $('<label style="display: none; margin-left: 10px;"><input type="checkbox" /> Add new</label>');
            $input.after($autocreate);


            $autocreate.click(function() {
                var $checkbox = $(this).find('input');
                if ($checkbox.is(':checked')) {
                    $hidden.val($input.val()).trigger('change');
                } else {
                    $hidden.val(value).trigger('change');
                }
            });
        }

        if (value && choice_list[value]) {
            $input.val(choice_list[value].label);
        }

        $input.autocomplete({
            minLength: 0,
            source: function( request, response ) {
                response( $.ui.autocomplete.filter(
                    choices, request.term
                ));
            },
            response: function(event, ui) {
                if (ui.content.length === 0) {
                    if ($input.val() && (!$hidden.val() || $input.val() != choice_list[$hidden.val()].label)) {
                        $hidden.val(value).trigger('change');
                        if (addNew===true) {
                        $autocreate.css('display', 'inline-block');
                        }
                    }
                } else {
                    if (addNew===true) {
                    $autocreate.hide();
                    }
                }
            },
            focus: function( event, ui ) {
                $(this).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $hidden.val(ui.item.value).trigger('change');
                return false;
            }
        })
        .focus(function() {
            $(this).autocomplete("search");
        })
        .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( '<a>' + item.value + ". " + item.label + '</a>' )
                .appendTo( ul );
        };

        this.setChoices = function(new_choices) {
            choice_list = new_choices;
            choices = _.values(new_choices);
            $input.blur();
            var currentValue = $input.val();
            if (currentValue && !choice_list[value]) {
                $input.val('');
                $hidden.val(value).trigger('change');
            }
        }
    };
})(jQuery, _);