jQuery(document).ready(function ($) {
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay,listWeek'
    },
    defaultView: 'agendaWeek',
    nowIndicator: true,
    events: {
      url: ajaxurl + "?action=load_lessons_ajax",
      error: function() {
        Swal.fire(
          'Error!',
          'Lesson schedule failed to load. Please report this issue to bugs@twocoda.com',
          'error'
        )
      }
    },
    loading: function (bool) {
      $('#loading').toggle(bool);
    }
  })
});
