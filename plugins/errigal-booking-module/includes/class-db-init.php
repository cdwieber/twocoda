<?php
/**
 * Errigal Booking Module Db_init.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */

/**
 * Errigal Booking Module Db_init.
 *
 * @since 0.0.0
 */
class EMB_Db_init {
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
	}

	private function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$appointment_table = $wpdb->prefix . "appointments";
		$appointment_type_table = $wpdb->prefix . "appointment_types";

		$sql = <<<SQL
CREATE TABLE $appointment_table (
  ID mediumint(9) NOT NULL AUTO_INCREMENT,
  user_id mediumint(9) NOT NULL,
  student_id mediumint(9),
  appointment_type tinyint NOT NULL,
  timestamp datetime TIMESTAMP NOT NULL,
  title varchar(255) NOT NULL,
  notes text NOT NULL,
  start_time bigint NOT NULL,
  end_time bigint NOT NULL,
  length_in_min int NOT NULL,
  cost int NOT NULL,
  recur_hash varchar(10) unique, /*this is a unique hash that ties all instances of recurring events together so they can be bulk edited */
  rrule varchar(255), /* RRULE for recurring events as described in RFC 5545 */ 
  PRIMARY KEY  (id)
) $charset_collate;

CREATE TABLE $appointment_type_table (
	id tinyint NOT NULL,
	type varchar(255) NOT NULL, 
)
SQL;


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		//Populate the types table
		$wpdb->insert(
			$appointment_type_table,
			array(
				array(
					'id' => 0,
					'type' => 'regular_appointment', // A general lesson.
				),
				array(
					'id' => 1,
					'type' => 'block_time' // Time blocked off by teacher.
				),
				array(
					'id' => 2,
					'type' => 'business_hours', // Business hours. Schedule inverse.
				),
				array(
					'id' => 3,
					'type' => 'cancelled'
				)
			)
		);

		add_option('em_booking_db_version', "0.1");
	}

	public function init() {
		$this->create_tables();
	}
}
