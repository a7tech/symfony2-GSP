/**
 * Created by Maciej Pyszyński
 */
(function($){
    var paymentsCalculation = function($el, options){
        var self = this;

        this.options = $.extend(true, this.options, options);
        this.$summary = options.summary instanceof jQuery ? options.summary : $(options.summary);
        this.$this = $el;

        this.$this.on('change keydown', ':input', function(){
            $.doTimeout('paymentsCalculation', 200, function(){
                self.recalculate();
            });
        });

        this.$this.on('row-removed', function(){
            self.recalculate();
        });

        $.each(self.options.summaryTargets, function(name, selector){
            self.targets[name] = self.$summary.find(selector);
        });

        //bind plugin to data variable
        this.$this.data('paymentsCalculation', this);
        return this;
    };

    paymentsCalculation.prototype = {
        $summary: null,
        $this: null,
        targets: {
            paid: null,
            due: null
        },
        options: {
            summary: null,
            rowClass: null,
            rowTargets: {
                amount: '.paid-amount'
            },
            summaryTargets: {
                paid: '.total-paid',
                due: '.due-total'
            },
            getTotal: null,
            paidCondition: null
        },

        recalculate: function(){
            var self = this,
                totals = {
                    paid: 0,
                    due: self.options.getTotal.apply(this)
                };


            this.$this.find(this.options.rowClass).each(function(i, row){
                var $row = $(row),
                    $paidAmount = $row.find(self.options.rowTargets.amount),
                    paidAmount = self.getValue($paidAmount);

                var is_paid = typeof(self.options.paidCondition) === 'function' ? self.options.paidCondition.apply(this, [$row, paidAmount]) : true;

                if(is_paid === true){
                    totals.paid += paidAmount;
                    totals.due -= paidAmount;
                }

            });

            //present the results
            $.each(self.targets, function(name, target){
               if(target.length > 0){
                   target.text(totals[name].toFixed(2));
               }
            });
        },

        getValue: function($object){
            var value = $object.is(':input') ? $object.val() : $object.text();

            value = value.replace(/\u00a0/g, '').replace(' ', '').replace(',', '.');

            value = parseFloat(value);
            if(isNaN(value)){
                value = 0;
            }

            return value;
        }
    };

    $.fn.paymentsCalculation = function(options){
        $(this).each(function(i, elem){
            new paymentsCalculation($(elem), options);
        });

        return this;
    };


})(jQuery);

