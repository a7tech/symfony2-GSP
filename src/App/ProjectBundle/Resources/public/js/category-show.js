$(function(){
        var $projectMainCategories = $("#main-categories"),
            orderUpdateUrl = $projectMainCategories.data('task-order-url');
        $("#main-categories").sortable({
            start: function (event, ui) {
                var $this = $(this),
                    $categoryWrapper = $this.closest('.project-wrapper'),
                    $tasks = $categoryWrapper.find(':not(.project-wrapper) .task-sortable-item'),
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
                    $categoryWrapper = $this.closest('.project-wrapper'),
                    $tasks = $categoryWrapper.find(':not(.project-wrapper) .task-sortable-item'),
                    $item = $(ui.item),
                    currentIndex = $tasks.index($item),
                    previousIndex = $categoryWrapper.data('index');

                if (currentIndex != previousIndex) {
//                    var $orderSource = currentIndex < previousIndex ? $item.next() : $item.prev(),
                     var $orderSource = $item.prev(),
                        order = $categoryWrapper.data('order');

                    overlay = $("<div></div>").prependTo("body").attr("id", "overlay");
                    $('.ajax-wheel').show();
                    $this.sortable('disable');
                    $.ajax({
                        url: orderUpdateUrl,
                        data: {
                            id: $item.data('id'),
                            order: $orderSource.data('order')
                        },
                        type: "POST",
                        success: function (response) {
                            $tasks.each(function (i, task) {
                                $(task).data('order', order[i]);
                            });
                            $.ajax({
                                url: "",
                                context: document.body,
                                success: function(s, x) {
                                  $this.sortable('enable');
                                  $('.ajax-wheel').hide();
                                  overlay.remove();
                                  return $(this).html(s);
                                }
                            });
                        },
                        dataType: 'json'
                    });
                    
                }
            }
        });
});
