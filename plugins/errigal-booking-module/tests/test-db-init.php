<?php
/**
 * Errigal Booking Module Db_init Tests.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */
class EMB_Db_init_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'EMB_Db_init' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'EMB_Db_init', errigal_booking_module()->db_init );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.0.0
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
