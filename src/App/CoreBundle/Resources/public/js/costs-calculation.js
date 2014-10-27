/**
 * Created by Maciej Pyszyński
 */
(function($){
    var costCalculation = function($el, options){
        var self = this;

        this.options = $.extend(true, {}, this.options, options);
        this.$summary = options.summary instanceof jQuery ? options.summary : $(options.summary);
        this.$this = $el;

        this.$this.on('change keydown', ':input', function(){
            $.doTimeout('costCalculation', 200, function(){
                self.recalculate();
            });
        });

        this.$this.on('row-removed', function(){
            self.recalculate();
        });

        this.targets = $.extend({}, this.targets);
        $.each(self.options.summaryTargets, function(name, selector){
            self.targets[name] = self.$summary.find(selector);
        });

        //bind plugin to data variable
        this.$this.data('costCalculation', this);
        return this;
    };

    costCalculation.prototype = {
        $summary: null,
        $this: null,
        targets: {
            discount: null,
            taxes: null,
            net: null,
            netWithoutDiscounts: null,
            total: null,
            due: null
        },
        options: {
            discount: false,
            taxes: false,
            quantity: true,
            summary: null,
            additionalNet: null,
            additionalTaxes: null,
            additionalTotal: null,
            done: null,
            rowClass: '.collection-row',
            rowTargets: {
                price: '.price',
                discount: '.discount',
                taxes: '.tax',
                quantity: '.quantity'
            },
            summaryTargets: {
                discount: '.discount-total',
                taxes: '.taxes-total',
                net: '.net-total',
                netWithoutDiscounts: '.net-without-discounts-total',
                total: '.total'
            }
        },

        recalculate: function(){
            var self = this,
                totals = {
                    netWithoutDiscounts: 0,
                    net: 0,
                    discount: 0,
                    taxes: 0,
                    total: 0
                };


            this.$this.find(this.options.rowClass).each(function(i, row){
                var $row = $(row),
                    $price = $row.find(self.options.rowTargets.price),
                    price = self.getValue($price);

                if(self.options.quantity){
                    var $quantity = $row.find(self.options.rowTargets.quantity),
                        quantity = self.getValue($quantity);

                    price *= quantity;
                }

                totals.netWithoutDiscounts += price;

                if(self.options.discount === true){
                    var $discount = $row.find(self.options.rowTargets.discount),
                        discount = self.getValue($discount)/100;

                    if(discount > 1){
                        discount = 1;
                    } else if(discount < 0) {
                        discount = 0
                    }

                    var discountAmount = parseFloat((price*discount).toFixed(2));


                    totals.discount += discountAmount;
                    price = price-discountAmount;
                }

                totals.net += price;

                var taxes = 0;
                if(self.options.taxes === true){
                    $row.find(self.options.rowTargets.taxes).each(function(i, tax){
                        var $tax = $(tax);

                        if($tax.is(':checked')){
                            taxes += parseFloat((price*parseFloat($(tax).data('tax'))).toFixed(2));
                        }
                    });

                    totals.taxes += taxes;
                }

                totals.total += price + taxes;
            });

            var additionalNet = 0;
            var additionalTaxes = 0;
            if(self.options.additionalNet !== null){
                additionalNet = self.options.additionalNet.call(this);
            }

            if(self.options.additionalTaxes !== null){
                additionalTaxes = self.options.additionalTaxes.call(this);
            }

            totals.net += additionalNet;
            totals.netWithoutDiscounts += additionalNet;
            totals.taxes += additionalTaxes;
            totals.total += additionalNet + additionalTaxes;
            totals.totalBeforeAdditional = totals.total;

            var additionalTotal = 0;
            if(self.options.additionalTotal !== null){
                additionalTotal = self.options.additionalTotal.call(this);
            }

            totals.total += additionalTotal;

            //present the results
            $.each(self.targets, function(name, target){
               if(target !== null && target.length > 0){
                   target.text(totals[name].toFixed(2));
               }
            });

            if(self.options.done !== null){
                self.options.done.call(this);
            }

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

    $.fn.costCalculation = function(options){
        $(this).each(function(i, elem){
            new costCalculation($(elem), options);
        });

        return this;
    };


})(jQuery);

