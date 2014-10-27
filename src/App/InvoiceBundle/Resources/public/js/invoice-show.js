$(function(){
    var $summary = $('#payment-summary');

    if($summary.length > 0) {
        $(".payments-form").paymentsCalculation({
            summary: $summary,
            rowClass: '.collection-row',
            rowTargets: {
                paid: '.paid-amount'
            },
            getTotal: function () {
                return parseFloat($summary.data('total'));
            },
            paidCondition: function ($row, amount) {
                return $row.find('.payment-date').val() != '';
            }
        }).data('paymentsCalculation').recalculate();

        //goto form
        var $formSection = $("#payments-wrapper");
        if ($formSection.data('goto')) {
            $('html,body').animate({
                scrollTop: $formSection.offset().top - 50
            }, 500);
        }
    }
});
