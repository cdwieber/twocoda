<?php
/**
 * Errigal Booking Module Appt_controller.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use Recurr\Transformer\ArrayTransformer;
use WeDevs\ORM\Eloquent\Facades\DB;
use \WeDevs\ORM\Eloquent\Database as Database;

class EMB_Appt_controller {
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
	public function __construct( EM_Booking $plugin ) {
		$this->plugin = $plugin;
	}

	public function get( int $id = null ) {

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

		// Get appointments within a +/- 6-hour vicinity.
		$existing_appts = $this->get_by_period(
			( $appt->start_time - ( HOUR_IN_SECONDS * 6 ) ),
			( $appt->end_time + ( HOUR_IN_SECONDS * 6 ) )
		);

		// If that turns up nothing, we're all good here.
		if ( ! $existing_appts ) {
			return false;
		}

		// Loop and compare. (No need for binary search trees here! Huzzah!)
		foreach ( $existing_appts as $existing_appt ) {
			// Is appointment start time within the existing appointment? Bzzzt.
			if ( $appt->start_time >= $existing_appt->start_time
				&& $appt->start_time <= $existing_appt->start_time ) {
					return true;
			}
			// Is appointment end time within existing appointment? Bzzzt.
			if ( $appt->end_time >= $existing_appt->start_time
				&& $appt->end_time <= $existing_appt->start_time ) {
					return true;
			}
			// Does appointment completely overlap existing appointment? Bzzzt.
			if ( $appt->start_time <= $existing_appt->start_time
				&& $appt->end_time >= $existing_appt->start_time ) {
					return true;
			}
		}
		// TODO: Write tests for everything but especially this...
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

	/**
	 * Take the appointment's length and start time and calculate an end time.
	 *
	 * Our front-end lib specifically wants to know the end time, length isn't enough.
	 *
	 * @param int|string $s Start time in either epoch or parsable string.
	 * @param int        $l length in minutes.
	 * @return int
	 */
	private function calculate_end_time( $s, int $l ) {
		// If start time is not a unix date, make it one
		if ( ! is_int( $s ) ) {
			$s = strtotime( $s );
		}
		return $s + ( $l * 60 );
	}

	/**
	 * Take the appointment object, examine the RRULE, and save the
	 * resulting appointments. Limit the recurrences to 50 to avoid infinite loops.
	 *
	 * BIG TODO: To complete this feature, we must implement some logic to keep "scheduling forward"
	 * infinitely recurring appointments.
	 *
	 * @param EMB_Appt_model $appt
	 * @throws \Recurr\Exception\InvalidWeekday
	 */
	private function handle_recurring_appointment( EMB_Appt_model $appt ) {
		$transformer = new ArrayTransformer();
			// TODO: Find some way to track the booking exceptions and send a warning to the
			// front end, i.e "The following appointments could not be scheduled:"
		$recur = $transformer->transform( $appt->rrule );

		$limiter = 1;
		// Save the existing model w/ atts, but update the datetimes.
		foreach ( $recur as $r ) {
			if ($limiter >= 50)
				break;

			$appt->start_time = $r->getStart();
			$appt->end_time   = $r->getEnd();

			$appt->save();

			$limiter++;
		}
	}

	/**
	 * Generate a unique hash that identifies all related appointments.
	 * In this way, we can work out "google-like" multiple appointment
	 * editing: do you want to change all appointments? Or just this one?
	 *
	 * @return string
	 */
	private function recur_hash_generate() {
		try {
			$hash = bin2hex( random_bytes( 32 ) );
		} catch ( Exception $e ) {
			wp_die( 'Caught recur hash exception: ' . $e );
		}
		return $hash;
	}

	/**
	 * Get the appointments between two given datetimes. Restrict to the users
	 * involved in the appointment (currently just the user/teacher and the student).
	 *
	 * @param int $period_start
	 * @param int $period_end
	 * @return null|\Illuminate\Support\Collection
	 */
	public function get_by_period( int $period_start, int $period_end ) {
		$db    = Errigal_Database::instance();
		$appts = $db->table( 'appointments' )
			->where( 'user_id', get_current_user_id() )
			->orWhere( 'student_id', get_current_user_id() )
			->whereBetween( 'start_time', [ $period_start, $period_end ] )->get();

		if ( $appts->isEmpty() ) {
			return null;
		}
		return $appts;
	}

	/**
	 * Produce a friendly title for the lesson, i.e. "30 Min Cello with Jan Smith".
	 *
	 * @param string $lesson_type
	 * @param int $student_id
	 * @return string
	 */
	private function lesson_title(string $lesson_type, int $student_id) {

		$student = get_userdata( $student_id );

		$title = $lesson_type . " with " . $student->display_name;

		return $title;
	}


	public function store( array $args ) {

		// If an ID is not passed, we're saving a new record
		if ( ! $args['ID'] ) {
			$appt = new EMB_Appt_model();
		} else {
			$appt = EMB_Appt_model::find( $args['ID'] );
		}

		$appt->user_id          = $args['user_id'];
		$appt->student_id       = $args['student_id'];
		$appt->lesson_type      = $args['lesson_type'];
		$appt->title            = $this->lesson_title($args['lesson_type'], $args['student_id']);
		$appt->start_time       = strtotime( $args['start_time'] );
		$appt->length_in_min    = $args['length_in_min'];
		$appt->end_time         = $this->calculate_end_time( $args['start_time'], $args['length_in_min'] );
		$appt->appointment_type = $args['appointment_type'];
		$appt->cost             = $args['cost'];
		$appt->notes			= $args['notes'];
		$appt->rrule            = $args['rrule'];

		if ( null != $appt->rrule ) {
			$this->handle_recurring_appointment( $appt );
		}

		try {
			$this->is_bookable( $appt );
		} catch ( EMB_Double_Booked_Exception | EMB_Past_Appointment_Exception $e ) {
			// Throw it to third base for a double play
			throw $e;
		}

		return $appt->save();
	}
}
