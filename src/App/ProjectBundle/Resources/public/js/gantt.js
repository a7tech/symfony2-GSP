$(function(){
    var $gantt_holder = $("#gantt_here"),
        free_days = $gantt_holder.data('free-days'),
        currentDate = new Date(),
        $overlay = $('#overlay');

    gantt.config.scale_height = 3*20;

    gantt.config.subscales = [
        {unit:"week", step:1, date: Translator.trans('gantt.scale.week', {}, 'Tasks')+" %W" },
        {unit:"month", step: 1, date:"%Y %F"},
        {unit:"day", step:1, date: "%d" }
    ];

    gantt.config.date_scale = "%D";
    gantt.config.grid_width = 450;
    gantt.config.duration_unit = "minute";
    gantt.config.duration_step = 1;
    gantt.config.time_step = 15;
    gantt.config.round_dnd_dates = false;
    gantt.config.fit_tasks = true;
    gantt.config.row_height = 20;
    gantt.config.min_column_width = 30;
    gantt.config.grid_width = 500;
    gantt.config.autosize = "y";

    //edit form
    gantt.config.buttons_left = [];
    gantt.config.buttons_right = ["dhx_cancel_btn","dhx_save_btn"];
    gantt.locale.labels.section_name = 'Name';
    gantt.locale.labels.section_start_date = 'Start date';
    gantt.locale.labels.section_end_date = 'End date';
    gantt.config.lightbox.sections = [
        {name:"name", height:38, type:"textarea", map_to:"task_name", focus:true},
        {name:"time", type:"time", map_to:"auto", focus:true, height: 38, time_format:["%d","%m","%Y","%H:%i"]},
    ];

    var freeDaysMarker = function(date){
        var day = date.getDay();
        if(day == 0){
            day =7;
        }

        return ($.inArray(day, free_days) < 0) ? "gantt-weekend" : '';
    };

    gantt.templates.scale_cell_class = freeDaysMarker;

    gantt.templates.task_cell_class = function(item,date){
        var cssClass = freeDaysMarker(date);

        if(currentDate.getDate() == date.getDate() && currentDate.getMonth() == date.getMonth() && currentDate.getFullYear() == date.getFullYear()){
            cssClass += " gantt-current-date";
        }

        return cssClass;
    };

    gantt.templates.progress_text = function(start, end, task){
        return "<span>"+Math.round(task.progress*100)+ "% </span>";
    };

    gantt.templates.rightside_text = function(start, end, task){
        return (task.tracker !== null ? "#" + task.tracker+' ' : '')+(task.status !== null ? task.status : '');
    };

    gantt.templates.tooltip_text = function(start,end,task){
        return task.tooltip;
    };

    gantt.config.columns = [
        {name:"task_id", label: 'ID', width: 35, template: function(item){
            return item.task_id !== null ? '<a href="'+ $gantt_holder.data('show-link').replace('_id_', item.task_id)+'">'+item.task_id+'</a>' : '';
        }},
        {name: "text",       label: Translator.trans('task_name', {}, 'Tasks'),  width: '*', tree:true },
        {name: "type", label: Translator.trans('gantt.task_type', {}, 'Tasks'), width: 30, template: function(item){
            return item.type !== null ? item.type : '';
        }},
        {name:"original_start_date", label: Translator.trans('start_date', {}, 'Tasks'), align: "center", width: 80, template: function(item){
            return item.start_date !== null && item.original_start_date != null ? item.original_start_date : '-';
        } },
        {name:"original_end_date",   label: Translator.trans('due_date', {}, 'Tasks'),   align: "center", width: 80, template: function(item){
            return item.end_date !== null && item.original_end_date != null ? item.original_end_date : '-';
        }},
        {name: 'formatted_duration', label: Translator.trans('estimated_time', {}, 'Tasks'), align: 'center', width: 80, template: function(item){
            return item.formatted_duration !== null ? item.formatted_duration : '-';
        }}
    ];

    gantt.templates.task_class = function(start, end, item){
        switch(item.id.substr(0, 1)){
            case 'p':
                return 'gantt-project';
                break;
            case 'c':
                return 'gantt-category';
                break;
            case 't':
                console.log(item.type);
                return item.original_start_date !== null || item.type == 'P' ? 'gantt-task' : 'gantt-hidden';
                break;
        }
    };

    gantt.config.drag_links = false;

    gantt.attachEvent('onBeforeTaskChanged', function(id, mode, item){
        return item.gantt_type === 'task';
    });

    gantt.attachEvent('onBeforeTaskUpdate', function(id, item){
        var postData = {
            id: item.id.substr(1),
            progress: item.progress,
            start_date: item.start_date,
            duration: item.duration
        };

        $overlay.show();
        $.ajax({
            url: $gantt_holder.data('update-url'),
            data: {
                task: postData
            },
            type: 'POST',
            success: function (gantt_data) {
                updateTasks(gantt_data);
                $overlay.hide();
            },
            dataType: 'json'
        });

        return true;
    });

    gantt.attachEvent("onBeforeTaskSelected", function(id,item){
        var $crudNav = $('.nav-crud');

        if(id.substr(0,1 )== 't') {
            $.each(['show', 'edit', 'delete'], function(i, action){
                var linkPattern = $gantt_holder.data(action+'-link');
                $crudNav.find('#'+action)
                    .removeClass('disabled')
                    .find('a')
                        .attr('href', linkPattern.replace('_id_', id.substr(1)))
                        .attr('target', '_blank');
            });
        } else {
            $crudNav.find('.crud')
                        .addClass('disabled')
                        .find('a')
                            .removeAttr('href').removeAttr('target');
        }
        return true;
    });

    gantt.init("gantt_here", new Date($gantt_holder.data('start-date')*1000), new Date($gantt_holder.data('end-date')*1000));
    gantt.parse(data);

    scrollToCurrent();

    $('.nav-crud #delete a').click(function(event){
        event.preventDefault();
        var $this = $(this),
            selectedId = gantt.getSelectedId();

        if($this.attr('href') !== undefined){
            $overlay.show();
            $.ajax({
                url: $this.attr('href'),
                success: function(gantt_data){
                    if(typeof(gantt_data.data[selectedId]) === 'undefined'){
                        gantt.deleteTask(selectedId);
                    }
                    updateTasks(gantt_data);

                    $overlay.hide();
                },
                dataType: 'json'
            });
        }
    });

    function scrollToCurrent() {
        //scroll to current date - 3 days
        var scrollDate = new Date();
        scrollDate.setDate(scrollDate.getDate() - 3);
        gantt.scrollTo(gantt.posFromDate(scrollDate));
    }

    function updateTasks(data){
        //tasks
        $.each(data.data, function(i, taskData){
            var ganttTask = gantt.getTask(taskData.id);

            $.each(taskData, function(prop, val){
                ganttTask[prop] = val;
            });
            ganttTask['start_date'] = parseDate(taskData.start_date);
            ganttTask['end_date'] = parseDate(taskData.end_date);
        });

        gantt.refreshData();

        //remove not used tasks

    }

    function parseDate(date){
        var parts = date.split(' '),
            dateParts = parts[0].split('-'),
            hourParts = parts[1].split(':');

        return new Date(parseInt(dateParts[2]), parseInt(dateParts[1])-1, parseInt(dateParts[0]), parseInt(hourParts[0]), parseInt(hourParts[1]), 0);
    }
});
