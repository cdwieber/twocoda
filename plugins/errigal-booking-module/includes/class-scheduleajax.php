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
		add_action( 'wp_ajax_save_appointment', [$this, 'save_appointment_ajax'] );
	}

	public function save_appointment_ajax() {
		$appt = new EMB_Appt_controller( $this->plugin );

		$args = [
			//TODO: title generator
			'title' => 'Temp Title',
			'user_id' => get_current_user_id(),
			'start_time' => $_POST['start_time'],
			'notes' => $_POST['notes'],
			'lesson_type' => $_POST['lesson_type'],
			'cost'  => 30, //ph
			'length_in_min' => 30, //ph
			'appointment_type' => 1,

		];

		$appt->store( $args );
		wp_die();
	}
}
