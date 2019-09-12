<?php
/**
 * Errigal Booking Module Appointment_type_model.
 *
 * @since   0.0.0
 * @package Errigal_Booking_Module
 */
use WeDevs\ORM\Eloquent\Model;
class EMB_Appointment_type_model extends Model {
	/**
	 * Appointments Table.
	 *
	 * @var string
	 */
	protected $table = 'appointment_type';

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

}
