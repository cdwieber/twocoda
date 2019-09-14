<?php

class EMB_Double_Booked_Exception extends Exception {
	protected $message = 'The specified appointment overlaps an existing appointment.';
}

class EMB_Past_Appointment_Exception extends Exception {
	protected $message = 'Sorry Doc Brown, but appointments cannot be scheduled in the past.';
}
