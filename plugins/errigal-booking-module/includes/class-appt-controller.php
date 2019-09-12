<?php
/**
 * Errigal Booking Module Appt_controller.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */

use Illuminate\Support\Facades\Request;
use WeDevs\ORM\Eloquent\Facades\DB;
use \WeDevs\ORM\Eloquent\Database as Database;

class EMB_Appt_controller {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.0
	 *
	 * @var   Errigal_Booking_Module
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.0
	 *
	 * @param  EM_Booking $plugin Main plugin object.
	 */
	public function __construct( EM_Booking $plugin ) {
		$this->plugin = $plugin;
	}

	public function get( int $id = null) {

	}

	/**
	 * Detect appointment conflicts.
	 *
	 * The basic idea is to find all appointments within a reasonable temporal
	 * vicinity of the passed appointment, then compare start and end times to
	 * ensure separation. The vicinity is +/- 6 hours from the start or end.
     * Most teachers scheduling 30-60 minute lessons will have a
	 * 12.5-13 hour vicinity scanned. This is probably more than necessary, but
	 * I want to get ahead of edge cases where a user, for example, tries to schedule
	 * within another appointment that for some reason is 12 hours.
	 *
	 * Note that this is the second level of validation;
	 * it should be validated on the front-end calendar as well.
	 *
	 * @param $appt
	 * @return bool
	 */
	private function is_conflict( EMB_Appt_model $appt ) {
		//Get appointments within a +/- 6-hour vicinity.
		$existing_appts = $this->get_by_period( ( $appt->start_time - ( HOUR_IN_SECONDS * 6 ) ),
			( $appt->end_time + ( HOUR_IN_SECONDS * 6 ) ) );

		// If that turns up nothing, we're all good here.
		if ( ! $existing_appts )
			return false;

		// Loop and compare. (No need for binary search trees here! Huzzah!)
		foreach ( $existing_appts as $existing_appt )  {
			// Is appointment start time within the existing appointment? Bzzzt.
			if ( $appt->start_time >= $existing_appt['start_time']
				&& $appt->start_time <= $existing_appt['end_time'] )
					return true;
			// Is appointment end time within existing appointment? Bzzzt.
			if ( $appt->end_time >= $existing_appt['start_time']
				&& $appt->end_time <= $existing_appt['end_time'] )
					return true;
			// Does appointment completely overlap existing appointment? Bzzzt.
			if ( $appt->start_time <= $existing_appt['start_time']
				&& $appt->end_time >= $existing_appt['end_time'] )
					return true;
		}

		// None of those triggered for any nearby appointment? Appointment does not conflict.
		return false;
	}

	/**
	 * Determine whether the appointment can be added to the calendar.
	 * Throw the appropriate exception if not.
	 *
	 * @param EMB_Appt_model $appt
	 * @return bool
	 * @throws EMB_Double_Booked_Exception
	 * @throws EMB_Past_Appointment_Exception
	 */
	private function is_bookable( EMB_Appt_model $appt ) {
		if ( true == $this->is_conflict( $appt ) ) {
			throw new EMB_Double_Booked_Exception();
		} elseif ( $appt->start_time < time() ) {
			throw new EMB_Past_Appointment_Exception();
		} else {
			return true;
		}
	}

	public function store( $args ) {

		$appt = new EMB_Appt_model();

		$appt->title = $args['title'];
		$appt->start_time = $args['start_time'];
		$appt->end_time = $args['end_time'];
		$appt->lesson_type = $args['lesson_type'];
		$appt->appointment_type = '';

		try {
			$this->is_bookable( $appt );
		} catch ( EMB_Double_Booked_Exception | EMB_Past_Appointment_Exception $e ) {
			return $e;
		}

		$appt->save();
		return true;
	}

	/**
	 * Get the appointments between two given datetimes. Restrict to the users
	 * involved in the appointment (currently just the user/teacher and the student).
	 *
	 * @param int $period_start
	 * @param int $period_end
	 * @return EMB_Appt_model
	 */
	public function get_by_period( int $period_start, int $period_end ) {
		return EMB_Appt_model::whereBetween('start_time', [$period_start, $period_end])
			->where('user_id', get_current_user_id())
			->orWhere('student_id', get_current_user_id())
			->get();
	}
}
