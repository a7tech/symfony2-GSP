$(function(){
        //search
    var $productsSearchTab = $("#products-search-tab"),
        $productsSearchTabResults = $("#search-results"),
        $productSearchForm = $("#products-search"),
        $searchSubmit = $("#btn-search"),
        lastSearch = null,

        //account profile
        $accountProfilePanel = $("#account-profile-select"),
        $accountProfileInput = $("#invoice_vendorCompany"),
        $purchaseEditPanel = $("#edit-purchase"),

        //products
        $productFormItemsHolder = $("#invoice_products"),

        //tasks
        $tasksFormItemsHolder = $(".tasks-items"),
        $notInvoicedTasks = $('.not-invoiced-tasks'),
        tasksInfo = $notInvoicedTasks.data('tasks-info'),

        $tasksCreditItemsHolder = $('.tasks-credit-items'),
        $notCreditedTasks = $('.not-credited-tasks'),
        notCreditedTasksInfo = $notCreditedTasks.data('tasks-info');



    $("#next-step").click(function(event){
        event.preventDefault();

        $accountProfilePanel.fadeOut('fast', function(){
            $purchaseEditPanel.fadeIn('fast');
            $('#vendorCompany').html($("#invoice_vendorCompany option:selected" ).text())
        });
    });

    var searchChange = function(){
        var $this = $(this);

        $.doTimeout('products-search', 200, function(){
            $productSearchForm.submit();
        });
    };

    $searchSubmit.click(function(event){
        event.preventDefault();

        if(lastSearch && lastSearch.readystate != 4){
            lastSearch.abort();
        }

        $productsSearchTabResults.html('Searching...');
        if ($('#invoice_vendorCompany').val()) {
            lastSearch = App.makeRequest({
                url: $productSearchForm.data('action'),
                data: $productSearchForm,
                additionalData: {
                    account_profile: $accountProfileInput.val()
                },
                dataType: 'html',
                success: function(response){
                    $productsSearchTab.click();
                    $productsSearchTabResults.html(response);
                }
            });
        }
    });

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
    });

    //add item
    $productsSearchTabResults.on('click', '.add-product', function(event){
        event.preventDefault();

        var $this = $(this),
            $row = $this.closest('tr'),
            id = $row.data('id'),
            $quantity = $row.find(".quantity"),
            quantity = parseInt($quantity.val()),
            price = $row.find('.price').val();

        var $existingItem = $productFormItemsHolder.find('.collection-row:has(.product[value="'+id+'"])');

        if($existingItem.length > 0){
            var $existingQuantity = $existingItem.find('.quantity'),
                quantityInCart = parseInt($existingQuantity.val());

            $existingQuantity.val(quantityInCart + quantity);
        } else {

            var $newItem = Admin.add_collection_row($productFormItemsHolder);

                $newItem.find('.quantity').val(quantity);
                $newItem.find('.price').val(price);
                $newItem.find('.product').val(id);
                $newItem.find('.discount').val(0);

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
        }

        $productFormItemsHolder.data('costCalculation').recalculate();
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

    //tasks
    $notInvoicedTasks.change(function(){
        var $this = $(this),
            val = $this.val();

        if(val != ''){
            $this.find('[value="'+val+'"]').hide();
            $this.val('');


            var $newItem = Admin.add_collection_row($tasksCreditItemsHolder),
                taskInfo = tasksInfo[val];

            $newItem.find('.task').val(val);
            $newItem.find('.task-id').text(val);
            $newItem.find('.task-name').text(taskInfo['name']);
            $newItem.find('.task-net').text(taskInfo['net']);
            $newItem.find('.task-description').html(taskInfo['description']);

            $newItem.find('.taxes input').each(function(index, tax_input){
                var $tax_input = $(tax_input);
                if($.inArray(parseInt($tax_input.val()), taskInfo.taxes) !== -1){
                    $tax_input.attr('checked', true);
                } else {
                    $tax_input.closest('li').remove();
                }
            });

            $tasksFormItemsHolder.data('costCalculation').recalculate();
        }
    });

    $tasksFormItemsHolder.on('row-removed', function(event, $row){
        var taskId = $row.find('.task').val(),
            $option = $notInvoicedTasks.find('[val="'+taskId+'"]');

        if($option.length > 0){
            $option.show();
        } else {
            var taskName = $row.find('.task-name').text(),
                taxes = [];

            $row.find('input.tax').each(function(index, tax){
                taxes.push(parseInt($(tax).val()));
            });

            tasksInfo[taskId] = {
                name: taskName,
                net: parseFloat($row.find('.price').text().replace(' ', '')),
                taxes: taxes
            };
            $notInvoicedTasks.append('<option value="'+taskId+'">'+taskName+' (Id '+taskId+')</option>');
        }
    });

    //tasks credit
    //tasks
    $notCreditedTasks.change(function(){
        var $this = $(this),
            val = $this.val();

        if(val != ''){
            $this.find('[value="'+val+'"]').hide();
            $this.val('');


            var $newItem = Admin.add_collection_row($tasksCreditItemsHolder),
                taskInfo = notCreditedTasksInfo[val];

            $newItem.find('.task').val(val);
            $newItem.find('.task-id').text(val);
            $newItem.find('.task-name').text(taskInfo['name']);
            $newItem.find('.task-net').text(taskInfo['net'] ? taskInfo['net'] : '-');
            $newItem.find('.task-description').html(taskInfo['description']);

            if(taskInfo['net'] !== null){
                $newItem.find('.task-credit').val(taskInfo['net']);
            }

            $newItem.find('.taxes input').each(function(index, tax_input){
                var $tax_input = $(tax_input);
                if($.inArray(parseInt($tax_input.val()), taskInfo.taxes) !== -1){
                    $tax_input.attr('checked', true);
                } else {
                    $tax_input.closest('li').remove();
                }
            });

            $tasksFormItemsHolder.data('costCalculation').recalculate();
        }
    });

    $tasksFormItemsHolder.on('row-removed', function(event, $row){
        var taskId = $row.find('.task').val(),
            $option = $notInvoicedTasks.find('[val="'+taskId+'"]');

        if($option.length > 0){
            $option.show();
        } else {
            var taskName = $row.find('.task-name').text(),
                taxes = [];

            $row.find('input.tax').each(function(index, tax){
                taxes.push(parseInt($(tax).val()));
            });

            tasksInfo[taskId] = {
                name: taskName,
                net: parseFloat($row.find('.price').text().replace(' ', '')),
                taxes: taxes
            };
            $notInvoicedTasks.append('<option value="'+taskId+'">'+taskName+' (Id '+taskId+')</option>');
        }
    });

    /***************************************************
     * Summaries
     ***************************************************/

    var $depositPosition = $("#deposit-position");

    //summary
    if($depositPosition.length == 0) {
        //regular summary
        $productFormItemsHolder.costCalculation({
            discount: true,
            taxes: true,
            summary: '#totals',
            summaryTargets: {
                totalBeforeAdditional: '.total-before-deposit'
            },
            additionalNet: function () {
                var $netTotal = $tasksFormItemsHolder.find('.net-total');
                return $netTotal.length > 0 ? parseFloat($netTotal.text().replace(/\u00a0/g, '').replace(' ', '')) : 0;
            },
            additionalTaxes: function () {
                var $taxesTotal = $tasksFormItemsHolder.find('.taxes-total');
                return $taxesTotal.length > 0 ? parseFloat($taxesTotal.text().replace(/\u00a0/g, '').replace(' ', '')) : 0;
            },
            additionalTotal: function() {
                var $deposit = $("#totals").find('.deposit');
                return $deposit.length > 0 ? parseFloat($deposit.text().replace(/\u00a0/g, '').replace(' ', '')) : 0;
            }
        }).data('costCalculation').recalculate();

        //tasks summary
        if ($tasksFormItemsHolder.length > 0) {
            $tasksFormItemsHolder.costCalculation({
                taxes: true,
                quantity: false,
                summary: '#tasks-totals',
                done: function () {
                    $productFormItemsHolder.data('costCalculation').recalculate();
                }
            }).data('costCalculation').recalculate();
        }
    } else {
        //deposit summary
        $depositPosition.costCalculation({
            taxes: true,
            quantity: false,
            summary: '#totals'
        }).data('costCalculation').recalculate();
    }



});
