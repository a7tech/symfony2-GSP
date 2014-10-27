$(function(){
    var $taskType = $('.task-type'),
        $payments = $('.payment'),
        $costType = $('.cost-type'),
        $paymentQuantity = $('.payment-quantity'),
        $projectSelect = $('.project-select'),
        projectTypes = $projectSelect.data('types'),
        $unitPrice = $('.unit-price'),
        $itemsQuantity = $(".items-quantity"),
        $makuUpInput = $(".mark-up-input"),
        $profitInput = $(".profit-input"),
        $sellingPrice = $("#selling-price-display");

    $taskType.data('options', $taskType.html());

    $taskType.change(adjustPayable);
    $costType.change(adjustQuantity);
    $projectSelect.change(adjustTaskTypes);

    adjustPayable();
    adjustQuantity();
    if($projectSelect.length > 0){
        adjustTaskTypes();
    }

    function adjustPayable()
    {
        var $inputs = $payments.find(":input"),
            val = $taskType.is(':enabled') ? $taskType.val() : $taskType.find('[selected]').attr('value');

        if(val == $payments.data('free')){
            $payments.hide();
            $inputs.removeAttr('required');
        } else {
            $payments.show();
            $inputs.attr('required', true);

            if(!$paymentQuantity.is(':visible')){
                $paymentQuantity.find(':input').removeAttr('required');
            }
        }
    }

    function adjustQuantity()
    {
        var $inputs =  $paymentQuantity.find(':input');

        if($costType.val() == $paymentQuantity.data('fixed')){
            $paymentQuantity.hide();
            $inputs.removeAttr('required');
        } else {
            $paymentQuantity.show();
            $inputs.attr('required', true);
        }
    }

    function adjustTaskTypes(){
        var val = $projectSelect.val();

        if (!val) {
            $taskType.children().remove();
            $taskType.prop('disabled', true);
            $payments.hide();

        } else {
            $taskType.prop('disabled', false);
        }

        if($taskType.is(':enabled')) {
            var val = $projectSelect.val(),
                projectType = projectTypes[val],
                payable = $taskType.data('payable'),
                adjustment = $taskType.data('adjustment');

            $taskType.html($taskType.data('options'));

            if (projectType == $projectSelect.data('draft')) {
                //draft
                $taskType.find('[value="' + adjustment + '"]').remove();

                if ($taskType.val() == adjustment) {
                    $taskType.val(payable).trigger('change');
                }
            } else {
                //project
                $taskType.find('[value="' + payable + '"]').remove();

                if ($taskType.val() == payable) {
                    $taskType.val(adjustment).trigger('change');
                }
            }
        }
    }

    /**
     * Rounded to 4 places after coma
     *
     * @param markup
     * @returns {number}
     */
    function calculateProfit(markup){
        return Math.round((markup/(100+markup))*1000000)/10000;
    }

    /**
     * Rounded to 4 places after coma
     *
     * @param markup
     * @returns {number}
     */
    function calculateMarkup(profit) {
        return Math.round((profit/(100-profit))*1000000)/10000;
    }

    function updateProfit(){
        var markup = parseFloat($makuUpInput.val());

        if(isNaN(markup)){
            markup = 0;
        }

        $profitInput.val(calculateProfit(markup));
    }

    function updateMarkup(){
        var profit = parseFloat($profitInput.val());

        if(isNaN(profit)){
            profit = 0;
        }

        $makuUpInput.val(calculateMarkup(profit));
    }

    function updatePrice(){

        var quantity = parseFloat($itemsQuantity.val()),
            markup = parseFloat($makuUpInput.val());

        if(isNaN(quantity)){
            quantity = 1;
        }

        if(isNaN(markup)){
            markup = 0;
        }

        var price = (parseFloat($unitPrice.val())*quantity)*((100+markup)/100);
        //round to 2 numbers
        price = Math.round(price*100)/100;

        $sellingPrice.text(price.toFixed(2));
    }

    $itemsQuantity.add($makuUpInput).add($unitPrice).on('keyup', function(){
        $.doTimeout('update-sell-price', 200, updatePrice);
    });
    $makuUpInput.on('keyup', function(){
        $.doTimeout('update-profit', 200, updateProfit);
    });
    $profitInput.on('keyup', function(){
        $.doTimeout('profit-updated', 200, function(){
            updateMarkup();
            updatePrice();
        });
    });

    //dependant task information
    var $dependantTasksInfo = $('.dependant-tasks');
    if($dependantTasksInfo.length > 0) {
        $(".task-status").change(function () {
            if($(this).val() == 5){
                //cancelled status
                $dependantTasksInfo.show();
            } else {
                $dependantTasksInfo.hide();
            }
        });
    }


    //lag information
    var $lagMessages = $('.lag-message span');


    function updateLagInformation(){
        var dependency = $('.task-dependency').val();

        $lagMessages.hide();

        if((dependency === '0' || dependency === '2') && $('.start-date').val() != ''){
            $lagMessages.filter('.start').show();
        } else if(dependency === '1' && $('.due-date').val() != ''){
            $lagMessages.filter('.end').show();
        }
    }

    updateLagInformation();
    $('.start-date, .due-date, .task-dependency').on('change', updateLagInformation);
});