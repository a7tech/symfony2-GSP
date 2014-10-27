(function($, _) {
    var AutocompleteWidget = this.AutocompleteWidget || {};
    this.AutocompleteWidget = AutocompleteWidget;

    AutocompleteWidget.multiple = function(form_vars) {
        var $input = form_vars.input;
        var $list = form_vars.list;
        var choices = _.values(form_vars.choices);
        var selected = form_vars.value;

        function addSelectedRow(item) {
            $( '<li>' )
                .append( item.value + '. ' + item.label )
                .append( '<input name="' + form_vars.full_name + '" value="' + item.value + '" type="hidden" />' )
                .append( ' <a href="#" data-id="' + item.value + '" class="collection-remove"><i class="icon-remove"></a>' )
                .appendTo( $list );
        }

        if (selected.length > 0) {
            selected.forEach(function(id) {
                addSelectedRow(form_vars.choices[id]);
            });
        }

        $input.autocomplete({
            minLength: 0,
            source: function( request, response ) {
                var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
                response( $.grep( choices, function( item ) {
                    return !_.contains( selected, item.value ) && matcher.test( item.value + '. ' + item.label );
                } ) );
            },
            focus: function( event, ui ) {
                $(this).val( ui.item.value + '. ' + ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                addSelectedRow( ui.item );
                selected.push( ui.item.value );
                $(this).val('').blur();
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

        $list.delegate('a.collection-remove', 'click', function() {
            if (confirm('You really want to remove?')) {
                var $btn = $(this);
                $btn.parents('li').remove();
                var id = $btn.data('id') + '';
                selected = _.without(selected, id);
                $input.val('').focus();
            }
        });
    }
})(jQuery, _);