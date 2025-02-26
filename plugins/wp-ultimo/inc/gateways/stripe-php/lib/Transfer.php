<?php

namespace WU_Stripe;

/**
 * Class Transfer
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $amount_reversed
 * @property mixed $application_fee
 * @property string $balance_transaction
 * @property int $created
 * @property string $currency
 * @property int $date
 * @property mixed $description
 * @property mixed $destination
 * @property mixed $destination_payment
 * @property mixed $failure_code
 * @property mixed $failure_message
 * @property mixed $fraud_details
 * @property mixed $invoice
 * @property bool $livemode
 * @property mixed $metadata
 * @property mixed $recipient
 * @property mixed $reversals
 * @property bool $reversed
 * @property mixed $source_transaction
 * @property string $source_type
 * @property mixed $statement_descriptor
 * @property string $status
 * @property string $type
 *
 * @package Stripe
 */
class Transfer extends ApiResource
{
    /**
     * @param string $id The ID of the transfer to retrieve.
     * @param array|string|null $opts
     *
     * @return Transfer
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Collection of Transfers
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Transfer The created transfer.
     */
    public static function create($params = null, $opts = null)
    {
        return self::_create($params, $opts);
    }

    /**
     * @param string $id The ID of the transfer to update.
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Transfer The updated transfer.
     */
    public static function update($id, $params = null, $options = null)
    {
        return self::_update($id, $params, $options);
    }

    /**
     * @return TransferReversal The created transfer reversal.
     */
    public function reverse($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/reversals';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);
        return $this;
    }

    /**
     * @return Transfer The canceled transfer.
     */
    public function cancel()
    {
        $url = $this->instanceUrl() . '/cancel';
        list($response, $opts) = $this->_request('post', $url);
        $this->refreshFrom($response, $opts);
        return $this;
    }

    /**
     * @param array|string|null $opts
     *
     * @return Transfer The saved transfer.
     */
    public function save($opts = null)
    {
        return $this->_save($opts);
    }
}
