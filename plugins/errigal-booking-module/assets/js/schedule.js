/**
 * Display js for scheduler view
 */

jQuery(document).ready(function ($) {

	let lessonModal = $('#lessonModal');
	let saveButton = $('#saveButton');

	// Initiate datepicker, match moment format to fullcal.
	$picker = jQuery('#datetimepicker').datetimepicker({
		uiLibrary: 'bootstrap4',
		footer: true,
		format: 'dd mmmm yyyy h:MM TT',
		use24hours: false
	});

	// Ensure the datepicker closes when the modal does.
	lessonModal.on('hide.bs.modal', function(e) {
		$picker.close();
	});

	// Kick off the save request.
	saveButton.on('click', function (e) {
		e.preventDefault();
		console.log("serial" + $('#lesson-form').serialize());
		$.ajax({
			type: 'POST',
			url: ajaxurl + "?action=save_appointment",
			datatype: 'json',
			data: $('#lesson-form').serialize(),
			success: function (response) {
				lessonModal.modal('hide');
				Swal.fire(
					'Success',
					'Lesson scheduled successfully.',
					'success'
				);
			},
			error: function () {
				Swal.fire(
					'Uh-oh!',
					'Something went wrong. Please go to twocoda.com/bugs and help us fix it.',
					'error'
				)
			}
		});


	});

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
			lessonModal.on('show.bs.modal', function (event) {
				var modal = $(this)
				modal.find('#datetimepicker').val(date.format('D MMMM YYYY h:mm A'))
			});
			lessonModal.modal('show');
		},
		loading: function (bool) {
			$('#loading').toggle(bool);
		}
	});

});
