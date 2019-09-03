<?php

/**
 * Plugin Name: Simple Feature Requests
 * Plugin URI: http://jckemp.com
 * Description: Customer led feature requests with voting.
 * Version: 1.0.5
 * Author: James Kemp
 * Author URI: https://jckemp.com
 * Text Domain: simple-feature-requests
 * Domain Path: /languages
 *
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class JCK_Simple_Feature_Requests
{
    /**
     * Version
     *
     * @var string
     */
    public static  $version = "1.0.5" ;
    /**
     * Full name
     *
     * @var string
     */
    public  $name = 'Simple Feature Requests' ;
    /**
     * @var null|JCK_SFR_Core_Settings
     */
    public  $settings = null ;
    /**
     * Class prefix
     *
     * @since  4.5.0
     * @access protected
     * @var string $class_prefix
     */
    protected  $class_prefix = "JCK_SFR_" ;
    /**
     * Construct
     */
    function __construct()
    {
        $this->define_constants();
        self::load_files();
        $this->load_classes();
    }
    
    /**
     * Define Constants.
     */
    private function define_constants()
    {
        $this->define( 'JCK_SFR_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'JCK_SFR_URL', plugin_dir_url( __FILE__ ) );
        $this->define( 'JCK_SFR_INC_PATH', JCK_SFR_PATH . 'inc/' );
        $this->define( 'JCK_SFR_VENDOR_PATH', JCK_SFR_INC_PATH . 'vendor/' );
        $this->define( 'JCK_SFR_TEMPLATES_PATH', JCK_SFR_PATH . 'templates/' );
        $this->define( 'JCK_SFR_ASSETS_URL', JCK_SFR_URL . 'assets/' );
        $this->define( 'JCK_SFR_BASENAME', plugin_basename( __FILE__ ) );
        $this->define( 'JCK_SFR_VERSION', self::$version );
    }
    
    /**
     * Define constant if not already set.
     *
     * @param string      $name
     * @param string|bool $value
     */
    private function define( $name, $value )
    {
        if ( !defined( $name ) ) {
            define( $name, $value );
        }
    }
    
    /**
     * Load files.
     */
    private static function load_files()
    {
        require_once JCK_SFR_INC_PATH . 'functions.php';
    }
    
    /**
     * Load classes
     */
    private function load_classes()
    {
        require_once JCK_SFR_INC_PATH . 'class-core-autoloader.php';
        JCK_SFR_Core_Autoloader::run( array(
            'prefix'   => 'JCK_SFR_',
            'inc_path' => JCK_SFR_INC_PATH,
        ) );
        $licence = JCK_SFR_Core_Licence::run( array(
            'basename' => JCK_SFR_BASENAME,
            'urls'     => array(
            'product'  => 'https://www.simplefeaturerequests.com/',
            'settings' => admin_url( 'admin.php?page=jck-sfr-settings' ),
            'account'  => admin_url( 'admin.php?page=jck-sfr-settings-account' ),
        ),
            'paths'    => array(
            'inc'    => JCK_SFR_INC_PATH,
            'plugin' => JCK_SFR_PATH,
        ),
            'freemius' => array(
            'id'                  => '1577',
            'slug'                => 'simple-feature-requests',
            'type'                => 'plugin',
            'public_key'          => 'pk_021142a45de2c0bcd8dc427adc8f7',
            'is_premium'          => true,
            'is_premium_only'     => false,
            'has_premium_version' => true,
            'has_addons'          => false,
            'has_paid_plans'      => true,
            'menu'                => array(
            'slug'   => 'jck-sfr-settings',
            'parent' => false,
        ),
        ),
        ) );
        $this->settings = JCK_SFR_Core_Settings::run( array(
            'vendor_path'   => JCK_SFR_VENDOR_PATH,
            'title'         => __( 'Simple Feature Requests', 'simple-feature-requests' ),
            'version'       => self::$version,
            'menu_title'    => __( 'Requests', 'simple-feature-requests' ),
            'page_title'    => __( 'Simple Feature Requests', 'simple-feature-requests' ),
            'parent_slug'   => false,
            'capability'    => 'manage_options',
            'settings_path' => JCK_SFR_INC_PATH . 'admin/settings.php',
            'option_group'  => 'jck_sfr',
            'docs'          => array(
            'collection'      => '/collection/134-woocommerce-attribute-swatches',
            'troubleshooting' => '',
            'getting-started' => false,
        ),
        ) );
        JCK_SFR_Settings::run();
        JCK_SFR_Assets::run();
        JCK_SFR_Post_Types::run();
        JCK_SFR_AJAX::run();
        JCK_SFR_User::run();
        JCK_SFR_Submission::run();
        JCK_SFR_Query::run();
        JCK_SFR_Template_Hooks::run();
        JCK_SFR_Factory::run();
        JCK_SFR_Notifications::run();
    }

}
$simple_feature_requests_class = new JCK_Simple_Feature_Requests();