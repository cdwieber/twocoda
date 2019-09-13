<?php
/**
 * Plugin Name: Errigal Booking Module
 * Plugin URI:  http://www.errigal.media
 * Description: A booking module for Errigal Media projects.
 * Version:     0.0.0
 * Author:      Chris Wieber
 * Author URI:  https://www.chriswieber.com
 * Donate link: http://www.errigal.media
 * License:     GPL v2
 * Text Domain: errigal-booking-module
 * Domain Path: /languages
 *
 * @link    http://www.errigal.media
 *
 * @package EM_Booking
 * @version 0.0.0
 *
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

/**
 * Copyright (c) 2019 Chris Wieber (email : chris@errigal.media)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


// Use composer autoload.
require 'vendor/autoload.php';

/**
 * Main initiation class.
 *
 * @since  0.0.0
 * @property string url
 */
final class EM_Booking {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	const VERSION = '0.0.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  0.0.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    EM_Booking
	 * @since  0.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of EMB_Db_init
	 *
	 * @since0.0.0
	 * @var EMB_Db_init
	 */
	protected $db_init;

	/**
	 * Instance of EMB_Appt_Model
	 *
	 * @since0.0.0
	 * @var EMB_Appt_model
	 */
	protected $appointment_model;

	/**
	 * Instance of EMB_Appt_controller
	 *
	 * @since0.0.0
	 * @var EMB_Appt_controller
	 */
	protected $appt_controller;

	/**
	 * Instance of EMB_Appointment_type_model
	 *
	 * @since0.0.0
	 * @var EMB_Appointment_type_model
	 */
	protected $appointment_type_model;

	/**
	 * Instance of EMB_Appointment_type_model
	 *
	 * @since0.0.0
	 * @var EMB_Admin_Schedule
	 */
	protected $admin_schedule;

	/**
	 * Instance of EMB_Scheduleajax
	 *
	 * @since0.0.0
	 * @var EMB_Scheduleajax
	 */
	protected $scheduleajax;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.0.0
	 * @return  EM_Booking A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  0.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.0.0
	 */
	public function plugin_classes() {


		$this->appointment_model = new EMB_Appt_model();
		$this->appt_controller = new EMB_Appt_controller( $this );
		$this->appointment_type_model = new EMB_Appointment_type_model();
		$this->admin_schedule = new EMB_Admin_Schedule( $this );
		$this->scheduleajax = new EMB_Scheduleajax( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  0.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  0.0.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		//Initialize the database tables.
		$this->db_init = new EMB_Db_init( $this );
		$this->db_init->init();

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  0.0.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  0.0.0
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'errigal-booking-module', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.0.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.0.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  0.0.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.0.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Errigal Booking Module is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'errigal-booking-module' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.0.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'db_init':
			case 'appointmentmodel':
			case 'appt_controller':
			case 'appointment_type_model':
			case 'scheduleajax':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}
}

/**
 * Grab the EM_Booking object and return it.
 * Wrapper for EM_Booking::get_instance().
 *
 * @since  0.0.0
 * @return EM_Booking  Singleton instance of plugin class.
 */
function errigal_booking_module() {
	return EM_Booking::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( errigal_booking_module(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( errigal_booking_module(), '_activate' ) );
register_deactivation_hook( __FILE__, array( errigal_booking_module(), '_deactivate' ) );
