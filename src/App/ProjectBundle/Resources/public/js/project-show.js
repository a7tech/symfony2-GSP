$(function(){
    var isEstimate = $("#project-info").data('estimate');

    if(isEstimate) {
        var $projectMainCategories = $("#main-project-categories"),
            orderUpdateUrl = $projectMainCategories.data('task-order-url');

        $(".main-project-categories .sortable-list").sortable({
            start: function (event, ui) {
                var $this = $(this),
                    $categoryWrapper = $this.closest('.project-category-wrapper'),
                    $tasks = $categoryWrapper.find(':not(.project-category-wrapper) .task-sortable-item'),
                    currentIndex = $tasks.index($(ui.item)),
                    order = [];

                $tasks.each(function (i, task) {
                    order.push($(task).data('order'));
                });

                $categoryWrapper.data('order', order);
                $categoryWrapper.data('index', currentIndex);
            },
            stop: function (event, ui) {
                var $this = $(this),
                    $categoryWrapper = $this.closest('.project-category-wrapper'),
                    $tasks = $categoryWrapper.find(':not(.project-category-wrapper) .task-sortable-item'),
                    $item = $(ui.item),
                    currentIndex = $tasks.index($item),
                    previousIndex = $categoryWrapper.data('index');

                if (currentIndex != previousIndex) {
                    var $orderSource = currentIndex < previousIndex ? $item.next() : $item.prev(),
                        order = $categoryWrapper.data('order');

                    $this.sortable('disable');
                    $.ajax({
                        url: orderUpdateUrl,
                        data: {
                            id: $item.data('id'),
                            order: $orderSource.data('order')
                        },
                        type: "POST",
                        success: function (resposne) {
                            $tasks.each(function (i, task) {
                                $(task).data('order', order[i]);
                            });
                            $this.sortable('enable');
                        },
                        dataType: 'json'
                    });
                }
            }
        });
    }
});
