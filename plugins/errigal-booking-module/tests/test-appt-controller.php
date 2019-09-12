<?php
/**
 * Errigal Booking Module Appt_controller Tests.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */
class EMB_Appt_controller_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'EMB_Appt_controller' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'EMB_Appt_controller', errigal_booking_module()->appt_controller );
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
