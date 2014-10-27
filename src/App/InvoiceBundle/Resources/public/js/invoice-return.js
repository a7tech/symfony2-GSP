$(function($) {
    Admin.setup_collections(document);

    var $productReturnsCollection = $('.product-returns-collection'),
        $tasksRefundsCollection = $('.task-returns-collection'),
        $tasksList = $('#task-list');

    // Task show
    $tasksList.on('click', '.add-task-refund', function(event) {
        event.preventDefault();

        var taskId = $(this).closest('tr').attr('data-task-id');
        $tasksRefundsCollection.find('tr[data-task-id='+taskId+']').show();
        $(this).hide();
    });

    // Task hide
    $tasksRefundsCollection.on('click', '.collection-hide', function(event) {
        event.preventDefault();

        var $collectionRow = $(this).closest('.collection-row');
        $collectionRow.hide();
        $tasksList.find('tr[data-task-id='+$collectionRow.attr('data-task-id')+'] .add-task-refund').show();

        // Clear fields
        $collectionRow.find('.refund-description').val('');
        $collectionRow.find('.refund-price').val('');
    });

    $('.add-product-return').click(function(event){
        event.preventDefault();

        var $this = $(this),
            $productRow = $this.closest('tr'),
            $returns = $productRow.find('input.product-return-quantity'),
            returns = parseInt($returns.val()),
            $refunds = $productRow.find('input.product-refund-quantity'),
            refunds = parseInt($refunds.val());

        if(returns > 0) {
            addRow($productRow, returns, false);
            $returns.val('');
        }

        if(refunds > 0) {
            addRow($productRow, refunds, true);
            $refunds.val('');
        }
    });

    $tasksRefundsCollection.on('row-removed', function(event, $row) {
        var taskId = $row.attr('data-task-id');
        $('#task-list tr[data-task-id='+taskId+'] .add-task-refund').show();
    });

    function addRow($productRow, number, refund){
        var $row = Admin.add_collection_row($productReturnsCollection);

        $row.find('.product-id').html($productRow.find('.product-id').html());
        $row.find('.product-name').text($productRow.find('.product-name').text());
        $row.find('.return-quantity').val(number);
        $row.find('.invoice-product-id').val($productRow.data('product-id'));
        $row.find('.return-type').val(refund ? 1 : 0);

        var refundPrice = refund ? $productRow.find('.product-price').text() : 0;
        $row.find('.refund-price').val(refundPrice);
    }
});
