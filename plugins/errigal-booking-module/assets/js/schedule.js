/**
 * Display js for scheduler view
 */

jQuery(document).ready(function ($) {

	let lessonModal = $('#lessonModal');
	let saveButton = $('#saveButton');
	let lessonForm = $('#lesson-form');
	let lessonType = $('#lesson_type');

	var cost;
	var length;

	// Get cost and length of lessons when selected
	var getCostAndLength = function(){
		cost = lessonType.find(':selected').data('cost');
		length = lessonType.find(':selected').data('cost');
	};
	getCostAndLength();
	// Ensure we update every time it changes.
	$(document).on('change',"#lesson_type", getCostAndLength);

	var clearForm = function() {
		$(':input',lessonForm)
			.not(':button, :submit, :reset, :hidden')
			.val('')
			.prop('checked', false)
			.prop('selected', false);
	};

	// Initiate datepicker, match moment format to fullcal.
	let picker = jQuery('#datetimepicker').datetimepicker({
		uiLibrary: 'bootstrap4',
		footer: true,
		format: 'dd mmmm yyyy h:MM TT',
		use24hours: false
	});

	// Ensure the datepicker closes when the modal does, as well as clear the form.
	lessonModal.on('hide.bs.modal', function(e) {
		picker.close();
		clearForm();
	});



	// Kick off the save request.
	saveButton.on('click', function (e) {
		e.preventDefault();

		var data = lessonForm.serialize();

		data = data + "&cost=" + cost + "&length=" + length;

		$.ajax({
			type: 'POST',
			url: ajaxurl + "?action=save_appointment",
			datatype: 'json',
			data: data,
			success: function (response) {
				lessonModal.modal('hide');
				calendar.fullCalendar('refetchEvents');
				Swal.fire(
					'Success',
					'Lesson scheduled successfully.',
					'success'
				);
			},
			error: function (response) {
				console.log(response);
				let message = response.responseJSON.data.message
				Swal.fire(
				'Uh-oh!',
				"There was an error: " + message,
				'error'
			)
		}
		});


	});

	//Init the calendar
	let calendar = $('#calendar').fullCalendar({
		customButtons: {
			addLesson: {
				text: 'Add New Lesson',
				click: function() {
					lessonModal.modal('show');
				}
			}
		},
		header: {
			left: 'prev,next today addLesson',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		businessHours: {
			// days of week. an array of zero-based day of week integers (0=Sunday)
			daysOfWeek: [ 1, 2, 3, 4, 5 ], // Monday - Thursday

			startTime: '9:00', // a start time (10am in this example)
			endTime: '16:00', // an end time (6pm in this example)
			},
		themeSystem: 'bootstrap4',
		defaultView: 'agendaWeek',
		nowIndicator: true,
		allDaySlot: false,
		events: {
			url: ajaxurl,
			type: 'GET',
			data: {
				action: 'load_appointments'
			},
			error: function() {
				Swal.fire(
					'Error!',
					'Lesson schedule failed to load. Please report this issue to bugs@twocoda.com',
					'error'
				)
			}
		},
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
