(function($) {
    var Admin = this.Admin || {};
    this.Admin = Admin;

    Admin.setup_collections = function(subject) {
        var $forms = $('form', subject);
        $forms.delegate('a.collection-add', 'click',
            function(e) {
                e.preventDefault();
                var collectionHolder = $(e.target).closest('[data-prototype]');
                Admin.add_collection_row(collectionHolder);
            }
        );
        
        $forms.on('click', 'a.collection-delete', function(e) {
            e.preventDefault();

            var $collectionRow = $(this).closest('.collection-row'),
                $collection = $collectionRow.closest('.collection');

            $collectionRow.remove();
            $collection.trigger('row-removed', [$collectionRow]);
        });
    };

    Admin.add_collection_row = function(collectionHolder) {
        var prototype = collectionHolder.data('prototype'),
            $form = $(prototype.replace(/__name__/g, collectionHolder.children().length));

        collectionHolder.append($form);
        Admin.bind_widgets();

        return $form;
    };

    Admin.bind_widgets = function() {
        $('.datepicker').datepicker({
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $(this).trigger('change');
            }
        });

        $('.datetimepicker').datetimepicker({
            timeFormat: 'HH:mm',
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $(this).trigger('change');
            }
        });

        $("select.select2").each(function(i, select){
            var $this = $(this);

            $this.select2({
                placeholder: "Chose option",
                allowClear: !$this.prop("required")
            });
        });

        Admin.widgets.filters();

        if(typeof(tinyMCE) !== 'undefined') {
            $('.tinymce').each(function () {
                tinyMCE.execCommand('mceAddControl', false, this.id);
                tinyMCE.triggerSave();
            });
        }

        Admin.bindAdditionalWidgets();
    };

    //override this if you need
    Admin.bindAdditionalWidgets = function(){};

    Admin.widgets = {
        filters: function(){
            $('.form-filters:not(.init)').each(function(index, formFilters){
                var $formFilters = $(formFilters),
                    $filtersSelector = $formFilters.find('.filters-selector');

                $filtersSelector.on('change', function(){
                    var $select = $(this),
                        $option = $select.find(":selected");

                    if($option.val() != ''){
                        $formFilters.find('.'+$option.val()).show();
                        $option.hide();
                    }

                    $filtersSelector.val('');
                });

                $formFilters.on('click', '.clear-filter', function(event){
                    event.preventDefault();
                    var $section = $(this).closest('.filter-section');

                    $section.find(':input').val('');
                    $section.find('.select2').select2('val', '');
                    $section.hide();
                    $filtersSelector.find('[value="'+$section.data('filter')+'"]').show();
                    $section.trigger('filter-cleared');
                });
            });
        }
    }

    Admin.bind_widgets();

})(jQuery);