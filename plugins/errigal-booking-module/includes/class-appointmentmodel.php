<?php
/**
 * Errigal Booking Module Appt_Model
 *
 * Abbreviated to "appt" because apparently if a class name is too long
 * Yeoman segfaults, which is a fun discovery.
 * With code copy-pasta from WP Eloquent docs; thanks Tareq.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */
use \WeDevs\ORM\Eloquent\Model as Model;

class EMB_Appt_model extends Model {

	/**
	 * Appointments Table.
	 *
	 * @var string
	 */
	protected $table = 'appointments';

	/**
	 * Define fillable fields.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'student_id',
		'appointment_type',
		'title',
		'lesson_type',
		'notes',
		'start_time',
		'end_time',
		'length_in_min',
		'cost',
		'recur_hash',
	];

	/**
	 * Disable eloquent's created_at/updated_at, MySQL timestamp is sufficient.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Set primary key as ID ("because wordpress" according to tareq)
	 *
	 * @var string
	 */
	protected $primaryKey = 'ID';

	/**
	 * Make ID guarded -- without this ID doesn't save.
	 *
	 * @var string
	 */
	protected $guarded = [ 'ID' ];

	/**
	 * Overide parent method to make sure prefixing is correct.
	 *
	 * @return string
	 */
	public function getTable()
	{
		if( isset( $this->table ) ){
			$prefix =  $this->getConnection()->db->prefix;
			return $prefix . $this->table;

		}

		return parent::getTable();
	}

	/**
	 * Define user relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function user() {
		return $this->hasOne('User', 'user_id');
	}

	/**
	 * Define student user relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function student() {
		return $this->hasOne('User', 'student_id');
	}

	/**
	 * Define type relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function appt_type() {
		return $this->has_one('EMB_Appointment_type_model');
	}
}
