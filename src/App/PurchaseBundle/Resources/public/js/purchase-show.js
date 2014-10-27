$(function(){
    var $summary = $('#payment-summary');

    $(".payments-form").paymentsCalculation({
        summary: $summary,
        rowClass: '.collection-row',
        rowTargets: {
            paid: '.paid-amount'
        },
        getTotal: function(){
            return parseFloat($summary.data('total'));
        },
        paidCondition: function($row, amount){
            return $row.find('.payment-date').val() != '';
        }
    }).data('paymentsCalculation').recalculate();
});
