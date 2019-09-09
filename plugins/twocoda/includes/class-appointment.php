<?php
/**
 * TwoCoda Core Appointment.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */

/**
 * TwoCoda Core Appointment.
 *
 * @since 0.0.1
 */
class TC_Appointment {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @var   TwoCoda_Core
	 */
	protected $plugin = null;

	/**
	 * Start time of the appointment in parsable string.
	 *
	 * @var string
	 */
	public $start_time;

	/**
	 * End time of the appointment in parsable string.
	 *
	 * @var string
	 */
	public $end_time;

	/**
	 * Title of appointment.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * ID of the user for which this appointment is scheduled.
	 * @var int
	 */
	public $user_id;

	/**
	 * Length of the appointment in minutes.
	 *
	 * @var int
	 */
	public $length;

	/**
	 * Post ID of the associated lesson.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
	 *
	 * @param  TwoCoda_Core $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Determine whether the appointment is bookable.
	 *
	 * If the appointment conflicts with another appointment (including buffer time)
	 * or occurs in the past, return false. Essential logic is:
	 *
	 * @param TC_Appointment $appointment appointment object
	 *
	 * @return bool
	 */
	private function is_bookable() {

		if ($this->get_appoinments_by_date_range($this->start_time, $this->end_time))
		return false;
	}

	/**
	 * Create the title of the lesson post
	 * from type and student name.
	 *
	 * @return string
	 */
	private function create_lesson_title() {

		$values = get_fields( $this->post_id );
		$new_title = $values['type_of_lesson'] . ' with ' . $values['student']->display_name;

		return $new_title;
	}

	public function get() {

	}

	public function get_appoinments_by_date_range($start, $end) {
		// query events
		$lessons = get_posts(array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'event',
			'meta_query' 		=> array(
				'relation' 			=> 'AND',
				array(
					'key'			=> 'start_date',
					'compare'		=> '<=',
					'value'			=> $start,
					'type'			=> 'DATETIME'
				),
				array(
					'key'			=> 'end_date',
					'compare'		=> '>=',
					'value'			=> $end,
					'type'			=> 'DATETIME'
				)
			),
			'order'				=> 'ASC',
			'orderby'			=> 'meta_value',
			'meta_key'			=> 'start_date',
			'meta_type'			=> 'DATE'
		));

		if( $lessons ) {
			return $lessons;
		}
	}

	public function store() {
		if ( ! $this->is_bookable() ) {
			return false;
		}
	}

}
