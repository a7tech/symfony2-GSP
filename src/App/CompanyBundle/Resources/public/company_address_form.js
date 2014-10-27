function setFields(data) {
    $div = '<option value="">Choose an option</option>';
    for (var i = 0; i < data.length; i++) {

        $div += '<option value="'+ data[i]["id"] +'">';

        if (data[i].contact) {
            $div = $div + '<strong>'+data[i].contact+'</strong>, ';
        }

        if(data[i].building) {
            $div = $div + data[i].building;
        }
        if(data[i].suite) {
            $div = $div + ', ' + data[i].suite;
        }
        if (data[i].street) {
            $div = $div + ', ' + data[i].street;
        }
        if (data[i].city) {
            $div = $div + ', ' + data[i].city
        }
        if (data[i].postcode) {
            $div = $div + ', ' + data[i].postcode;
        }
        if(data[i].region) {
            $div = $div + ', ' + data[i].region;
        }
        if(data[i].province) {
            $div = $div + ', ' + data[i].province;
        }
        if (data[i].country) {
            $div = $div + ', ' + data[i]["country"];
        }

        $div += '</option>';
    }

    return $div;
}

function initCompanyAddressForm(ajax_company_shipping_address_url, ajax_company_billing_address_url ) {
    $(document).on('change', ".company-autocomplete input[type='hidden']", function() {
        var $company = $(this);
        var companyId = $company.val();

        if (companyId) {
            var shippingUrl = ajax_company_shipping_address_url.replace('__company_id__', companyId);
            var billingUrl = ajax_company_billing_address_url.replace('__company_id__', companyId);
            $.getJSON(shippingUrl, function(data) {
                $input = $("input[id$='_customerCompany_autocomplete']");
                var $div="";
                $div = setFields(data);

                jQuery('select[id*="invoice_shipment"]').html('');
                jQuery('select[id*="invoice_shipment"]').html($div);
                jQuery('select[id*="invoice_shipment"]').removeAttr('disabled');

            });
            $.getJSON(billingUrl, function(data) {
                $input = $("input[id$='_customerCompany_autocomplete']");
                var $div="";
                $div = setFields(data);
                jQuery('select[id*="invoice_billing"]').html('');
                jQuery('select[id*="invoice_billing"]').html($div);
                jQuery('select[id*="invoice_billing"]').removeAttr('disabled');

            });
        }
    });
}

function initCustomerAddressForm(ajax_customer_shipping_address_url, ajax_customer_billing_address_url) {
    $(document).on('change', ".person-autocomplete input[type='hidden']", function() {
        var $company = $(".company-autocomplete input[type='hidden']");

        if($company.val() == '') {
            var $customer = $(this);
            var customerId = $customer.val();
            if (customerId) {
                var shippingUrl = ajax_customer_shipping_address_url.replace('__customer_id__', customerId);
                var billingUrl = ajax_customer_billing_address_url.replace('__customer_id__', customerId);
                $.getJSON(shippingUrl, function (data) {
                    $input = $("input[id$='_customer_autocomplete']");
                    var $div = "";
                    $div = setFields(data);

                    jQuery('select[id*="invoice_shipment"]').html('');
                    jQuery('select[id*="invoice_shipment"]').html($div);
                    $('select[id*="invoice_shipment"]').removeAttr('disabled');

                });
                $.getJSON(billingUrl, function (data) {
                    $input = $("input[id$='_customer_autocomplete']");
                    var $div = "";
                    $div = setFields(data);
                    jQuery('select[id*="invoice_billing"]').html('');
                    jQuery('select[id*="invoice_billing"]').html($div);
                    $('select[id*="invoice_billing"]').removeAttr('disabled');

                });
            }
        }
    });
}