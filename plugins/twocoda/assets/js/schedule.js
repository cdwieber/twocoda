document.addEventListener('DOMContentLoaded', function() {

    var returned_events;
    jQuery(document).ready(function($){
        $.ajax({
            url: tcajax.ajax_url,
            type: 'get',
            data: {
                action: 'load_lessons_ajax'
            },
            success: function(response) {
                 returned_events = response;
                 console.log(returned_events);

                 var calendarEl = document.getElementById('calendar');

                 var calendar = new FullCalendar.Calendar(calendarEl, {
                   plugins: [ 'dayGrid', 'timeGrid', 'list', 'interaction' ],
                   themeSystem: 'minty',
                   header: {
                     left: 'prev,next today',
                     center: 'title',
                     right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                   },
                   defaultDate: '2019-08-12',
                   navLinks: true, // can click day/week names to navigate views
                   editable: true,
                   eventLimit: true, // allow "more" link when too many events
                   events: returned_events
                 });
                 calendar.render();
            }
        })



    
  });


  });