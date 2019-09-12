<?php
/**
 * Errigal Booking Module Appointmentmodel Tests.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */
class EMB_Appointmentmodel_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'EMB_Appointmentmodel' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'EMB_Appointmentmodel', errigal_booking_module()->appointmentmodel );
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
