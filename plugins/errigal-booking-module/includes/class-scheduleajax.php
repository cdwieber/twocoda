<?php
/**
 * Errigal Booking Module Scheduleajax.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */
class EMB_Scheduleajax {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.0
	 *
	 * @var   EM_Booking
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.0
	 *
	 * @param  EM_Booking $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.0
	 */
	public function hooks() {
		// Save endpoint
		add_action( 'wp_ajax_save_appointment', [ $this, 'save_appointment_ajax' ] );
		// Load endpoint
		add_action( 'wp_ajax_load_appointments', [ $this, 'load_appointments_ajax' ] );
		// Reschedule endpoint
		add_action( 'wp_ajax_reschedule', [ $this, 'reschedule' ] );
		// Get by ID
		add_action( 'wp_ajax_get_by_id', [ $this, 'get_by_id' ] );
	}

	public function save_appointment_ajax() {
		$appt = new EMB_Appt_controller( $this->plugin );

		$args = [
			'ID'			   => $_POST['id'],
			'user_id'          => get_current_user_id(),
			'student_id'       => $_POST['student'],
			'start_time'       => $_POST['start_time'],
			'notes'            => $_POST['notes'],
			'lesson_type'      => $_POST['lesson_type'],
			'cost'             => $_POST['cost'],
			'length_in_min'    => $_POST['length'],
			'appointment_type' => 1,
		];

		try {
			$appt->store( $args );
		} catch ( Exception $e ) {
			// Grab the error message from the exception and send it to the frontend
			wp_send_json_error( [ 'message' => $e->getMessage() ], 500 );
			return false;
		}
		wp_die();
	}

	/**
	 * Get apppointments for requested period and send to frontend.
	 */
	public function load_appointments_ajax() {
		$appt = new EMB_Appt_controller( $this->plugin );

		$from = strtotime( $_GET['start'] );
		$to   = strtotime( $_GET['end'] );

		$lessons = $appt->get_by_period( $from, $to );

		if ( ! $lessons ) {
			return;
		}
			$lessons = $lessons->toArray();


		// TODO: ACF Options field for timezone

		// Grab the appointments and format them into fullcal-friendly events
		$lesson_events = [];
		foreach ( $lessons as $lesson ) {
			$lesson->start = ( gmdate( 'Y-m-d\TH:i:s', $lesson->start_time ) );
			$lesson->end   = ( gmdate( 'Y-m-d\TH:i:s', $lesson->end_time ) );

			$event['id']    = $lesson->ID;
			$event['title'] = $lesson->title;
			$event['start'] = $lesson->start;
			$event['end']   = $lesson->end;

			$lesson_events[] = $event;
		}

		$lesson_events = json_encode( $lesson_events );
		echo $lesson_events;
		wp_die();
	}

	/**
	 * Handle drag n' drop rescheduling on the front end.
	 *
	 * Also, for the moment, clear recurrent status so as to break it away from
	 * the rest of the set.
	 */
	public function reschedule() {
		$db = Errigal_Database::instance();
		$db->table( 'appointments' )
			->where( 'ID', $_POST[ 'id' ] )
			->update([
				'start_time'=> strtotime( $_POST[ 'start' ] ),
				'end_time'  => strtotime( $_POST[ 'end' ] ),
				'rrule'     => '',
			]);
		wp_die();
	}

	/**
	 * Get event object by ID via AJAX request.
	 */
	public function get_by_id() {
		$db = Errigal_Database::instance();
		$lesson = $db->table( 'appointments' )
			->find( $_REQUEST['id'] );

		wp_send_json($lesson, 200);

		wp_die();
	}
}
