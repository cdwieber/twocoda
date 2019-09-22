/**
 * JS for scheduler view
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
		length = lessonType.find(':selected').data('length');
	};
	getCostAndLength();
	// Ensure we update every time it changes.
	$(document).on('change',"#lesson_type", getCostAndLength);

	var clearForm = function() {
		lessonModal.find('#lesson-modal-title').html("New Lesson");
		lessonModal.find('#datetimepicker').val('');
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
		// If the hidden field contains an ID, then we know we're editing existing
		if ("" !== lessonModal.find("#id").val()) {
			Swal.fire({
				title: "Save Changes?",
				text: "You're about to alter an existing lesson. Continue?",
				confirmButtonText: "Save Changes",
				showCancelButton: true,
				allowOutsideClick: false
				}).then((res) => {
				if(res.value) {
					saveLesson();
				} else if(res.dismiss === 'cancel') {
					return 0;
				} else if(res.dismiss === 'esc') {
					return 0;
				}
			});
		} else {
			saveLesson();
		}
	});

	let saveLesson = function() {
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
				let message = response.responseJSON.data.message;
				Swal.fire(
					'Uh-oh!',
					"There was an error: " + message,
					'error'
				)
			}
		});
	};

	/**
	 * Handle drag n' drop rescheduling
	 * @param id
	 * @param start
	 * @param end
	 */
	let handleDrop = function(id, start, end) {
		var start_time = moment(start).format('Y-MM-DD HH:mm:ss');
		var end_time = moment(end).format('Y-MM-DD HH:mm:ss');

		$.ajax({
			url: ajaxurl + '?action=reschedule',
			type: 'post',
			data: {
				id: id,
				start: start_time,
				end: end_time
			},
			success: function() {
				Swal.fire(
					'Success',
					'Lesson successfully rescheduled',
					'success',
				);
			}
		});
	};

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


		eventStartEditable: true,
		eventOverlap: false,

		eventClick: function(event) {
			lessonModal.find('#lesson-modal-title').html("Edit " + event.title);
			lessonModal.find('#datetimepicker').val(event.start.format('D MMMM YYYY h:mm A'));
			console.log(event.start.format('D MMMM YYYY h:mm A'));

			lessonModal.find("#id").val(event.id);
			lessonModal.find("#saveButton").html(
				"<span class=\"spinner-grow spinner-grow-sm\" role=\"status\" aria-hidden=\"true\"></span>\n" +
				"  <span class=\"sr-only\">Loading...</span>" +
				"Loading..."
			).prop('disabled', true);

			$.ajax({
				url: ajaxurl + '?action=get_by_id',
				type: 'get',
				data: {
					id: event.id
				},
				success: function (response) {
					console.log(response);
					lessonModal.find("#student").val(response.student_id);
					lessonModal.find("#lesson_type").val(response.lesson_type);
					lessonModal.find("#lesson_location").val(response.lesson_location);
					lessonModal.find('#notes').text(response.notes);
					lessonModal.find("#saveButton").html("Save").prop('disabled', false);
					getCostAndLength();
			}
			});

			console.log(event);
			lessonModal.modal('show');
		},

		eventDrop: function(event, delta, revertFunc) {

			Swal.fire({
				title: "Reschedule?",
				text: "You're about to reschedule "
					+ event.title + " to "
					+  event.start.format('D MMMM YYYY h:mm A')
					+ ". The student will be notified.",
				type: "warning",
				showCancelButton: true,
				confirmButtonText: "Reschedule",
				allowOutsideClick: false,
			}).then((res) => {
				if(res.value){
					console.log(event);
					handleDrop(event.id, event.start, event.end);
				}else if(res.dismiss === 'cancel'){
					revertFunc();
				}
				else if(res.dismiss === 'esc'){
					revertFunc();
				}
			});
		},

		dayClick: function(date, jsEvent, view) {
			lessonModal.find('#datetimepicker').val(date.format('D MMMM YYYY h:mm A'))
			lessonModal.modal('show');
		},
		loading: function (bool) {
			$('#loading').toggle(bool);
		}
	});

});
