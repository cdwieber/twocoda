<?php
/**
 * TwoCoda Core Booking. All business logic for booking.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */

/**
 * TwoCoda Core Scheduling Admin Module.
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
		add_action('acf/save_post', [$this, 'update_lesson_on_save'], 15);

		add_action( 'wp_ajax_load_lessons_ajax', [$this, 'load_lessons_ajax'] );
	}

	public function load_lessons_ajax() {
		//TODO: Intelligently load posts so we don't fetch the
		//entire dataset every time...

		//TODO: End time from duration
		$args = array(
			'numberposts' => -1,
			'post_type'   => 'tc-lesson'
		);

		$q = new WP_Query($args);

		$lessons = [];
		if ($q->have_posts()) {
			while ($q->have_posts()) {
				$q->the_post();
			$lessons[] = [
				'title' => get_the_title(),
				'start' => get_field('date_and_time'),
				'end'	=> get_field('end_time'),
				'url'   => '/wp-admin/post.php?post=' . get_the_ID() . '&action=edit'
				];
			}
		}
		$lessons = json_encode($lessons);
		echo $lessons;
		wp_die();
	}

	public function update_lesson_on_save($post_id) {
		//Only hook into lessons
		$post_type = get_post_type($post_id);
		if ( "tc-lesson" != $post_type ) return;

		//Update the title so it automatically reads [lesson] with [person].
		$new_title = $this->create_lesson_title($post_id);
		wp_update_post(['ID' => $post_id, 'post_title' => $new_title]);

		//Automatically calculate an endtime based on the lesson type.
		$start_time = get_field('date_and_time', $post_id);
		$lesson_type = get_field('type_of_lesson', $post_id);
		try {
			$end_time = $this->calculate_lesson_end_time($start_time, $lesson_type);
		} catch (Exception $e) {
			echo "Caught Exception", $e->getMessage();
		}

		update_field('end_time', $end_time, $post_id);
	}

	/**
	 * Create the title of the lesson post
	 * from type and student name.
	 *
	 * @return string
	 */
	private function create_lesson_title($post_id) {

		$values = get_fields( $post_id );
		$new_title = $values['type_of_lesson'] . ' with ' . $values['student']->display_name;

		return $new_title;
	}

	/**
	 * Calculate the lesson's end time from the selected type's length.
	 * @throws Exception
	 */
	private function calculate_lesson_end_time($start_time, $this_lesson_type) {
		//Find the current lesson's length in minutes based off of the options field.
		if (have_rows('lesson_types','option')) {
			while (have_rows('lesson_types','option')) {
				the_row();
				$set_lesson_name = get_sub_field('lesson_name');
				if ($set_lesson_name == $this_lesson_type) {
					$length_in_minutes = get_sub_field('length_in_minutes');
				}
			}
		}

		//Add this to the dt object
		$dt = new DateTime($start_time);
		$dt->add(new DateInterval('PT'.$length_in_minutes.'M'));

		return $dt->format('Y-m-d H:i');
	}
}
