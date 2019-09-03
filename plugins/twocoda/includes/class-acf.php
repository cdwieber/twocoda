<?php
/**
 * TwoCoda Core Acf.
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
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {

	}

	/**
	 * Create the options pages to be populated by ACF
	 *
	 * @return void
	 */
	public function create_acf_options_pages() {
		if( function_exists('acf_add_options_page') ) {
	
			acf_add_options_page(array(
				'page_title' 	=> 'Policy Customizer',
				'menu_title'	=> 'Policies',
				'menu_slug' 	=> 'twocoda-policies',
				'capability'	=> 'edit_posts',
				'redirect'		=> false
			));
			
			acf_add_options_sub_page(array(
				'page_title' 	=> 'Theme Header Settings',
				'menu_title'	=> 'Header',
				'parent_slug'	=> 'theme-general-settings',
			));
			
			acf_add_options_sub_page(array(
				'page_title' 	=> 'Theme Footer Settings',
				'menu_title'	=> 'Footer',
				'parent_slug'	=> 'theme-general-settings',
			));
			
		}
	}
}
