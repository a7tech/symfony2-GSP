// keep track of how many email fields have been rendered
$(document).ready(function () {


    if (jQuery(".datepicker")) {
        jQuery(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "1800:2013",
            dateFormat: 'dd-mm-yy'
        });
    }




    jQuery('html').removeClass('no-js');
    Admin.add_pretty_errors(document);
    Admin.add_collapsed_toggle();
    Admin.add_filters(document);
    Admin.set_object_field_value(document);
    Admin.setup_collection_buttons(document);
    Admin.setup_per_page_switcher(document);


});

