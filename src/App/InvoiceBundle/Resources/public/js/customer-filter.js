/**
 * Created by nastya on 1/30/14.
 */

function initCustomerFilter(ajax_customer_filter_url) {
    $('.company-autocomplete input[type="hidden"]').on('change', function() {
        var $this = $(this);
        var companyId = $this.val();
        if (companyId) {
            var url = ajax_customer_filter_url.replace('__companyId_', companyId);
            $.getJSON(url, function(data) {
                var $autocomplete = $this.closest('form').find("input[id$='invoice_customer_autocomplete']");
                var customerCategoryWidget = $autocomplete.data('person-widget');
                customerCategoryWidget.setChoices(data.choices);
            });
        }
    });
};