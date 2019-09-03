<?php
/**
 * TwoCoda.
 *
 * @since   0.0.1
 * @package TwoCoda
 */
class TwoCoda_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.1
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'TwoCoda') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  0.0.1
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'TwoCoda', twocoda() );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.0.1
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
