$(function(){
    //deposit invoice
    var $depositAmount = $('.deposit-amount'),
        $depositPercentage = $('.deposit-percent');

    $depositAmount.on('keyup', function(){
        var $this = $(this);

        $.doTimeout('recalculate-deposit-percentage', 200, function(){
            var percentage = parseFloat($this.val())/$this.data('project-total');
            //round & normalize
            percentage = Math.round(percentage*1000000)/10000;

            $depositPercentage.val(percentage);
        });
    });

    $depositPercentage.on('keyup', function(){
        var $this = $(this);

        $.doTimeout('recalculate-deposit-percentage', 200, function(){
            var val = Math.round(parseFloat($this.val())*10000)/1000000,
                amount = val * $depositAmount.data('project-total');

            amount = Math.round(amount*100)/100;

            $depositAmount.val(amount);
        });
    });
});
