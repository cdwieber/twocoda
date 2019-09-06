<?php
/**
 * TwoCoda Core Acf. Hooks and helpers for custom ACF stuff.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */

/**
 * TwoCoda Core Acf.
 *
 * @since 0.0.1
 */
class TC_Acf {
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
		$this->create_acf_options_pages();
		$this->create_acf_policy_fields();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {
		add_filter('acf/load_field/name=select_user', [$this, 'populate_users_in_field']);
		add_filter('acf/load_field/name=lesson_page_location', [$this, 'acf_load_locations']);
		add_filter('acf/load_field/name=type_of_lesson', [$this, 'acf_load_types']);
	}

	/**
	 * Create the options pages to be populated by ACF
	 *
	 * @return void
	 */
	public function create_acf_options_pages() {
		if( function_exists('acf_add_options_page') ) {
	
			acf_add_options_page(array(
				'page_title' 	=> 'Business & Policies',
				'menu_title'	=> 'Business & Policies',
				'menu_slug' 	=> 'twocoda-policies',
				'capability'	=> 'edit_posts',
				'redirect'		=> false
			));
		}
	}

	/**
	 * Define all ADF policy fields.
	 *
	 *
	 * @return void
	 */
	public function create_acf_policy_fields() {
		require_once 'acf-config/policies.inc.php';
	}

	public function create_acf_lesson_fields() {
		require_once 'acf-config/lessons.inc.php';
	}

	/**
	 * Populate users in a given field. Add a filter for the
	 * desired field in hooks().
	 *
	 * @param [type] $field
	 * @return void
	 */
	public function populate_users_in_field( $field )
	{	
		// reset choices
		$field['choices'] = array();
		
		$users = get_users();
		
		foreach ($users as $user) {
			$field['choices'][ $user->ID ] = $user->display_name;
		}

		return $field;
	}

	public function acf_load_locations( $field ) {

		$field['choices'] = array();

		if (have_rows('locations', 'option')) {
			while( have_rows('locations', 'option')) {
				the_row();
				$name = get_sub_field('location_name');
				$field['choices'][$name] = $name;
			}
		}

		return $field;
	}

	public function acf_load_types( $field ) {
		$field['choices'] = array();

		if (have_rows('lesson_types', 'option')) {
			while( have_rows('lesson_types', 'option')) {
				the_row();
				$name = get_sub_field('lesson_name');
				$field['choices'][$name] = $name;
			}
		}

		return $field;
	}
}
