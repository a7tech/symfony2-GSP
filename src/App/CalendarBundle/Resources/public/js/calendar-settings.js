$(function () {
    var date = new Date(),
        d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear(),
        $calendarFilters = $("#calendar-filters"),
        $calendarHolder = $('#calendar-holder');

    $calendarHolder.fullCalendar({
        header: {
            left: 'prev, title, next, agendaDay,agendaWeek,month,',
            center: '',
            right: 'Assigned to me'
        },
        dayClick: function(date, allDay, jsEvent, view) {
           $('#myModal .modal-body').load(
                Routing.generate('backend_calendar_event_new', {'date': date}),
                function(e){
                    $('#myModal').modal('show');
                });
        },
        eventMouseover: function(calEvent,jsEvent) {
            //alert('mouesd over');
        },
        editable: true,
        eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
           var url = Routing.generate('backend_calendar_event_update_js', {'id': event.id});
           var data = {
                'event': event,
            };

           $.ajax({
             url: url,
             type: 'PUT',
             data: data,
             success: function(result) {
                // Do something with the result
             }
           });
        },
        eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
           var url = Routing.generate('backend_calendar_event_update_js', {'id': event.id});
           var data = {
                'event': event,
            };

           $.ajax({
             url: url,
             type: 'PUT',
             data: data,
             success: function(result) {
                // Do something with the result
             }
           });
        },
        defaultView: 'month',
        weekNumbers: true,
        lazyFetching:true,
        timeFormat: {
                // for agendaWeek and agendaDay
                agenda: 'h:mmt', // 5:00 - 6:30

                // for all other views
                '': 'h:mmt'            // 7p
        },
        eventSources: [
                {
                    url: Routing.generate('backend_fullcalendar_loader'),
                    type: 'POST',
                    data: function(){
                        return App.serializePost($calendarFilters);
                    },
                    error: function() {
                       //alert('There was an error while fetching Google Calendar!');
                    }
                }
        ],
        eventRender: function(event, element){
            if(typeof(event.details) !== 'undefined'){
                element.attr('data-content', event.details);
                element.tooltip({
                    content: event.details,
                    items: '[data-content]',
                    position: {
                        my: 'left top+5'
                    },
                    hide: {
                        delay: 1000
                    },
                    tooltipClass: 'calendar-tooltip'
                });
            }
        }
    });
    // Move the selects into the right place on the page
    if($calendarFilters.length > 0){
        $('.fc-header').after($calendarFilters);
        $calendarFilters.removeClass('hide');
    }

    //filters changed
    $calendarFilters.on('change', ':input[name]', function(){
        $calendarFilters.trigger('submit');
    });

    $calendarFilters.on('submit', function(event){
        event.preventDefault();

        $calendarHolder.fullCalendar('refetchEvents');
    });

    $calendarFilters.on('filter-cleared', function(){
        $calendarFilters.trigger('submit');
    });

    // Check the url, if it has day in it, change the view to day
    var URL = $(location).attr('href');
    var page = URL.substring(URL.lastIndexOf('/') + 1);
    if(page == 'month'){
        $('#calendar-holder').fullCalendar( 'changeView', 'month' );
    }
    if(page == 'day'){
        $('#calendar-holder').fullCalendar( 'changeView', 'agendaDay' );
    }
});