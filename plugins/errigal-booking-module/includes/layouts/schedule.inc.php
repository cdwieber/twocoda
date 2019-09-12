<div id="calendar">
</div>

<div id="loading">
	<img src="<?php echo errigal_booking_module()->url ?>/assets/img/ajax-loader.gif" />
</div>



<div class="modal fade" id="lessonModal">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-header" id="lesson-modal-title">New Lesson</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input id="datetimepicker" />
				</div>
				<div class="form-group">
					<label for="student">Student</label>
					<select class="form-control" id="student">
						<option>Billy Mays</option>
						<option>Hansel Hansel</option>
						<option>Rocky Rococo</option>
					</select>
				</div>
				<div class="form-group">
					<label for="lesson_type">Lesson Type</label>
					<select class="form-control" id="lesson_type">
						<option>Billy Mays</option>
						<option>Hansel Hansel</option>
						<option>Rocky Rococo</option>
					<select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="saveButton">Save</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>

</script>
