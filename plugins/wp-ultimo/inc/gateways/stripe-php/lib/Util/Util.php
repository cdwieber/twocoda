<?php

namespace WU_Stripe\Util;

use WU_Stripe\StripeObject;

abstract class Util
{
    private static $isMbstringAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     *
     * @param array|mixed $array
     * @return boolean True if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

      // TODO: generally incorrect, but it's correct given Stripe's response
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively converts the PHP Stripe object to an array.
     *
     * @param array $values The PHP Stripe object to convert.
     * @return array
     */
    public static function convertStripeObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }
            if ($v instanceof StripeObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertStripeObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the Stripe API to the corresponding PHP object.
     *
     * @param array $resp The response from the Stripe API.
     * @param array $opts
     * @return StripeObject|array
     */
    public static function convertToStripeObject($resp, $opts)
    {
        $types = array(
            'account' => 'WU_Stripe\\Account',
            'alipay_account' => 'WU_Stripe\\AlipayAccount',
            'apple_pay_domain' => 'WU_Stripe\\ApplePayDomain',
            'bank_account' => 'WU_Stripe\\BankAccount',
            'balance_transaction' => 'WU_Stripe\\BalanceTransaction',
            'card' => 'WU_Stripe\\Card',
            'charge' => 'WU_Stripe\\Charge',
            'country_spec' => 'WU_Stripe\\CountrySpec',
            'coupon' => 'WU_Stripe\\Coupon',
            'customer' => 'WU_Stripe\\Customer',
            'dispute' => 'WU_Stripe\\Dispute',
            'list' => 'WU_Stripe\\Collection',
            'invoice' => 'WU_Stripe\\Invoice',
            'invoiceitem' => 'WU_Stripe\\InvoiceItem',
            'event' => 'WU_Stripe\\Event',
            'file' => 'WU_Stripe\\FileUpload',
            'token' => 'WU_Stripe\\Token',
            'transfer' => 'WU_Stripe\\Transfer',
            'transfer_reversal' => 'WU_Stripe\\TransferReversal',
            'order' => 'WU_Stripe\\Order',
            'order_return' => 'WU_Stripe\\OrderReturn',
            'plan' => 'WU_Stripe\\Plan',
            'product' => 'WU_Stripe\\Product',
            'recipient' => 'WU_Stripe\\Recipient',
            'refund' => 'WU_Stripe\\Refund',
            'sku' => 'WU_Stripe\\SKU',
            'source' => 'WU_Stripe\\Source',
            'subscription' => 'WU_Stripe\\Subscription',
            'subscription_item' => 'WU_Stripe\\SubscriptionItem',
            'three_d_secure' => 'WU_Stripe\\ThreeDSecure',
            'fee_refund' => 'WU_Stripe\\ApplicationFeeRefund',
            'bitcoin_receiver' => 'WU_Stripe\\BitcoinReceiver',
            'bitcoin_transaction' => 'WU_Stripe\\BitcoinTransaction',
        );
        if (self::isList($resp)) {
            $mapped = array();
            foreach ($resp as $i) {
                array_push($mapped, self::convertToStripeObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = 'WU_Stripe\\StripeObject';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded. Ask your system " .
                    "administrator to enable the mbstring extension, or write to " .
                    "support@stripe.com if you have any questions.", E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }
}
