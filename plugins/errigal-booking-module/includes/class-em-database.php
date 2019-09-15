<?php
/**
 * Extend Tareq88's eloquent DB class so that we can
 * change to the installation base_prefix.
 */

use WeDevs\ORM\Eloquent\Builder;
use WeDevs\ORM\Eloquent\Database;

class Errigal_Database extends Database {
	/**
	 * Begin a fluent query against a database table.
	 *
	 * @param  string $table
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function table( $table ) {
		$processor = $this->getPostProcessor();

		$table = $this->db->base_prefix . $table;

		$query = new Builder( $this, $this->getQueryGrammar(), $processor );

		return $query->from( $table );
	}
	/**
	 * Initializes the Database class
	 *
	 * @return \WeDevs\ORM\Eloquent\Database
	 */
	public static function instance() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}
