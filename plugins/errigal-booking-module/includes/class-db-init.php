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
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  title varchar(255) NOT NULL,
  notes text NOT NULL,
  start_time bigint NOT NULL,
  end_time bigint NOT NULL,
  length_in_min int NOT NULL,
  cost int NOT NULL,
  recur_hash varchar(10) unique,
  rrule varchar(255),
  PRIMARY KEY (ID)
);

CREATE TABLE $appointment_type_table (
  ID tinyint NOT NULL,
  type varchar(255) NOT NULL,
  PRIMARY KEY (ID)
);

INSERT INTO $appointment_type_table (ID, type)
VALUES (0, 'regular'),(1, 'block_time')
SQL;


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option('em_booking_db_version', "0.1");
	}

	public function init() {
		 $this->create_tables();
	}
}
