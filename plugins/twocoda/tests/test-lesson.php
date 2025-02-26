<?php
/**
 * TwoCoda Core Lesson Tests.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */
class TC_Lesson_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.1
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'TC_Lesson') );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.1
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'TC_Lesson', twocoda()->lesson' );
	}

	/**
	 * Test to make sure the CPT now exists.
	 *
	 * @since  0.0.1
	 */
	function test_cpt_exists() {
		$this->assertTrue( post_type_exists( 'tc-lesson' ) );
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
