<?php
/**
 * TwoCoda Core Booking. All business logic for booking.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */

/**
 * TwoCoda Core Booking.
 *
 * @since 0.0.1
 */
class TC_Booking {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @var   TwoCoda_Core
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
	 *
	 * @param  TwoCoda_Core $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {
		add_action('acf/save_post', [$this, 'create_lesson_title'], 5);
	}

	public function load_lessons_ajax() {

	}

	/**
	 * Create the title of the lesson post
	 * from type and student name.
	 *
	 * @return void
	 */
	public function create_lesson_title($post_id) {
		
		$post_type = get_post_type($post_id);
		if ( "tc-lesson" != $post_type ) return;

		$values = get_fields( $post_id );
		//print_r($values);die;

		$new_title = $values['type_of_lesson'] . ' with ' . $values['select_user'];

		wp_update_post([$post_id, 'post_title' => $new_title]);
	}
}
