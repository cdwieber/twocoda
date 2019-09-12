/**
 * Display js for scheduler view
 */

jQuery(document).ready(function ($) {
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		themeSystem: 'bootstrap4',
		defaultView: 'agendaWeek',
		nowIndicator: true,
		allDaySlot: false,
		// events: {
		// 	url: ajaxurl,
		// 	error: function() {
		// 		Swal.fire(
		// 			'Error!',
		// 			'Lesson schedule failed to load. Please report this issue to bugs@twocoda.com',
		// 			'error'
		// 		)
		// 	}
		// },
		dayClick: function(date, jsEvent, view) {
			$('#lessonModal').on('show.bs.modal', function (event) {
				var modal = $(this)
				modal.find('#datetimepicker').val(date.format('D MMMM YYYY h:mm A'))
			});
			$('#lessonModal').modal('show');
		},
		loading: function (bool) {
			$('#loading').toggle(bool);
		}
	});

});

$picker = jQuery('#datetimepicker').datetimepicker({
	uiLibrary: 'bootstrap4',
	footer: true,
	format: 'dd mmmm yyyy h:MM TT',
	use24hours: false
});
//Ensure the datepicker closes when the modal does.
jQuery('#lessonModal').on('hide.bs.modal', function(e) {
	$picker.close();
});


