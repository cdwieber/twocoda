<?php
$students = get_users( [ 'role' => 'student' ] );
?>

<div id="calendar">
	<div id="loading">
		<div class="spinner-grow text-success" role="status">
			<span class="sr-only">Loading...</span>
		</div>
	</div>
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
				<form id="lesson-form" action="#">
					<input type="hidden" name="id" id="id" value="">
				<div class="form-group">
					<input id="datetimepicker" name="start_time" required/>
				</div>
				<div class="form-group">
					<label for="student">Student</label>
					<select class="form-control" id="student" name="student">
						<option></option>
						<?php
						if ( $students ) {
							foreach ( $students as $student ) {
								echo '<option value="' . $student->ID . '"">' . $student->display_name . '</option>';
							}
						} else {
							echo '<option disabled>No students found!</option>';
						}
						?>
					</select>
				</div>
					<div class="form-group">
						<label for="lesson_type">Lesson Type</label>
						<select class="form-control" id="lesson-type" name="lesson_type" required>
							<option></option>
							<?php
							if ( have_rows( 'lesson_types', 'option' ) ) {
								while ( have_rows( 'lesson_types', 'option' ) ) {
									the_row();

									$value  = get_sub_field( 'lesson_name' );
									$length = get_sub_field( 'length_in_minutes' );
									$cost   = get_sub_field( 'cost' );

									echo "<option value='{$value}' data-length='{$length}' data-cost='{$cost}'>$value</option>";
								}
							} else {
								echo '<option disabled>See policies page to add at least one lesson type.</option>';
							}
							?>
						<select>
					</div>
					<div class="form-group">
						<label for="location">Lesson Location</label>
						<select class="form-control" id="lesson-location" name="lesson_location" required>
							<option></option>
							<option value="online">Online</option>
							<option value="student_home">Student's Home</option>
							<?php
							if ( have_rows( 'locations', 'option' ) ) {
								while ( have_rows( 'locations', 'option' ) ) {
									the_row();

									$value = get_sub_field( 'location_name' );

									echo "<option value='{$value}'>$value</option>";
								}
							} else {
								echo '<option disabled>See your policies page to add more.</option>';
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label for="notes">Lesson Notes</label>
						<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" id="saveButton">Save</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>

</script>
