<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * JCK_SFR_Licence.
 *
 * @class    JCK_SFR_Licence
 * @version  1.0.0
 * @category Class
 * @author   Iconic
 */
class JCK_SFR_Licence
{
    /**
     * Run.
     */
    public static function run()
    {
        self::configure();
        self::add_filters();
    }
    
    /**
     * Configure.
     */
    public static function configure()
    {
        global  $jck_sfr_fs ;
        
        if ( !isset( $jck_sfr_fs ) ) {
            require_once JCK_SFR_INC_PATH . 'vendor/freemius/start.php';
            $jck_sfr_fs = fs_dynamic_init( array(
                'id'             => '1577',
                'slug'           => 'simple-feature-requests',
                'type'           => 'plugin',
                'public_key'     => 'pk_021142a45de2c0bcd8dc427adc8f7',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => false,
                'menu'           => array(
                'slug'    => 'edit.php?post_type=cpt_feature_requests',
                'contact' => false,
                'support' => false,
                'account' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $jck_sfr_fs;
    }
    
    /**
     * Add filters.
     */
    public static function add_filters()
    {
        global  $jck_sfr_fs ;
        $jck_sfr_fs->add_filter(
            'templates/account.php',
            array( __CLASS__, 'back_to_settings_link' ),
            10,
            1
        );
    }
    
    /**
     * Account link.
     */
    public static function account_link()
    {
        return sprintf( '<a href="%s" class="button button-secondary">%s</a>', admin_url( 'admin.php?page=iconic-wap-settings-account' ), __( 'Manage Licence &amp; Billing', 'iconic-wap' ) );
    }
    
    /**
     * Contact link.
     */
    public static function contact_link()
    {
        global  $jck_sfr_fs ;
        return sprintf( '<a href="%s" class="button button-secondary">%s</a>', $jck_sfr_fs->contact_url(), __( 'Create Support Ticket', 'iconic-wap' ) );
    }
    
    /**
     * Get contact URL.
     */
    public static function get_contact_url( $subject = false, $message = false )
    {
        global  $jck_sfr_fs ;
        return $jck_sfr_fs->contact_url( $subject, $message );
    }
    
    /**
     * Back to settings link.
     */
    public static function back_to_settings_link( $html )
    {
        return $html . sprintf( '<a href="%s" class="button button-secondary">&larr; %s</a>', admin_url( 'admin.php?page=iconic-wap-settings' ), __( 'Back to Settings', 'iconic-wap' ) );
    }
    
    /**
     * Has valid licence.
     *
     * @return bool
     */
    public static function has_valid_licence()
    {
        global  $jck_sfr_fs ;
        if ( $jck_sfr_fs->can_use_premium_code() ) {
            return true;
        }
        return false;
    }

}