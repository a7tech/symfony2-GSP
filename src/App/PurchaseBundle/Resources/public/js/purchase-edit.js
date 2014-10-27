$(function(){
    var $productsSearchTab = $("#products-search-tab"),
        $productsSearchTabResults = $("#search-results"),
        $accountProfileInputCopy = $("#account-profile-copy"),
        $accountProfilePanel = $("#account-profile-select"),
        $purchaseEditPanel = $("#edit-purchase"),
        $productSearchForm = $("#products-search"),
        $productForm = $("#product-form"),
        $productFormItemsHolder = $("#backend_purchase_items"),
        $accountProfileInput = $("#backend_purchase_accountProfile"),
        $accountProfileName = $('#account-profile'),
        $purchaseItems = $('.purchase-items'),
        $paymentTab = $("#payment"),
        lastSearch = null;

    var searchChange = function(){
        var $this = $(this);

        $.doTimeout('products-search', 200, function(){
            $productSearchForm.submit();
        });
    };

    $productSearchForm.submit(function(event){
        event.preventDefault();

        var $form = $(this);

        if(lastSearch && lastSearch.readystate != 4){
            lastSearch.abort();
        }

        $productsSearchTabResults.html('Searching...');

        lastSearch = App.makeRequest({
            url: $form.attr('action'),
            data: $form,
            additionalData: {
                account_profile: $accountProfileInputCopy.val()
            },
            dataType: 'html',
            success: function(response){
                $productsSearchTab.click();
                $productsSearchTabResults.html(response);
            }
        })
    }).on('keyup', 'input', searchChange)
    .on('change', 'select', searchChange);

    searchChange();

    $("#order-tabs").on('click', 'li a', function(event){
        event.preventDefault();
        var $this = $(this),
            $target = $($this.attr('href')),
            $tab_content = $target.closest('.tab-content'),
            $siblings = $tab_content.find('.tab-pane').not($target);

        $siblings.hide();
        $target.show();

        $this.tab('show');
        $this.removeClass('tab-with-errors');
    });

    $(".errors").closest('.tab-pane').each(function(i, pane){
        var $pane = $(pane);
        $('.nav-tabs li:not(.active) a[href="#'+$pane.attr('id')+'"]').addClass('tab-with-errors');
    });

    $accountProfileInput.change(function(){
        $accountProfileInputCopy.val($(this).val());
        $accountProfileName.text($accountProfileInput.find(':selected').text());
    });
    $accountProfileInputCopy.val($accountProfileInput.val());

    $("#next-step").click(function(event){
        event.preventDefault();

        $accountProfilePanel.fadeOut('fast', function(){
            $purchaseEditPanel.fadeIn('fast');
        });
    });

    $(".save-purchase").click(function(event){
        event.preventDefault();

        if($(this).attr('name') == 'proceed'){
            $('.purchase-is-draft').val(0);
        }

        $productForm.submit();
    });

    //add item
    $productsSearchTabResults.on('click', '.add-product', function(event){
        event.preventDefault();

        var $this = $(this),
            $row = $this.closest('tr'),
            id = $row.data('id'),
            $quantity = $row.find(".quantity"),
            quantity = $quantity.val(),
            price = $row.find('.price').val();

        var $newItem = Admin.add_collection_row($productFormItemsHolder);

        $newItem.find('.quantity').val(quantity);
        $newItem.find('.price').val(price);
        $newItem.find('.product').val(id);
        $newItem.find('.suppliers').html($row.data('suppliers'));

        $row.find('.product-copy').each(function(i, property){
            var $property = $(property);
            $newItem.find('.'+$property.data('target')).html($property.html());
        });

        var taxes = $row.data('taxes');

        $newItem.find('.taxes input').each(function(index, tax_input){
            var $tax_input = $(tax_input);
            if($.inArray(parseInt($tax_input.val()), taxes) !== -1){
                $tax_input.attr('checked', true);
            } else {
                $tax_input.closest('li').remove();
            }
        });

    }).on('click', '.pagination a', function(event){
        event.preventDefault();

        App.makeRequest({
            url: $(this).attr('href'),
            dataType: 'html',
            success: function(response){
                $productsSearchTab.click();
                $productsSearchTabResults.html(response);
            }
        })
    });

    /*******************************
     * Totals
     *******************************/

    $purchaseItems.costCalculation({
        taxes: true,
        summary: "#totals"
    }).data('costCalculation').recalculate();

    /***************************
     * Supplier payments
     ******************************/
    var $supplierPaymentsHolder = $("#backend_purchase_payments");

    $purchaseItems.on('change', '.suppliers', function(){
        normalizePayments();
    });

    /**
     * Not needed for now
     */
    function normalizePayments(){
        var suppliers = {};

        $purchaseItems.find('.suppliers').each(function(index, select){
            var $select = $(select);
            suppliers[$select.val()] = $select.find('option:selected').text();
        });

        var $suppliersPayments = $paymentTab.find('.supplier-payment');

        // remove not needed payments
        $suppliersPayments.each(function(index, supplierPayment){
            var $supplierPayment = $(supplierPayment);

            if(typeof(suppliers[$supplierPayment.data('supplier-id')]) === 'undefined'){
                $supplierPayment.remove();
            }
        });

        //add new payments
        $.each(suppliers, function(supplierId, supplierName){
            if($suppliersPayments.filter('[data-supplier-id="'+supplierId+'"]').length == 0){
                var $newPayment = $($supplierPaymentsHolder.data('prototype').replace(/__supplier__/g, supplierId));
                $newPayment.find('label:first').text(supplierName+'*');

                $supplierPaymentsHolder.append($newPayment);
            }
        });
    }
});
