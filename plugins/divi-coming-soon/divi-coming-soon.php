<?php
/*
Plugin Name: Divi Coming Soon
Plugin URL: https://divilife.com/
Description: Easily create a coming soon page using the Divi Builder!
Version: 1.0.1
Author: Divi Life — Tim Strifler
Author URI: https://divilife.com
*/

// Make sure we don't expose any info if called directly or may someone integrates this plugin in a theme
if ( class_exists('DiviComingSoon') || !defined('ABSPATH') || !function_exists( 'add_action' ) ) {
	
	return;
}

define( 'DIVI_COMINGSOON_VERSION', '1.0');
define( 'DIVI_COMINGSOON_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'DIVI_COMINGSOON_PLUGIN_NAME', trim( dirname( DIVI_COMINGSOON_PLUGIN_BASENAME ), '/' ) );
define( 'DIVI_COMINGSOON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DIVI_COMINGSOON_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once( DIVI_COMINGSOON_PLUGIN_DIR . '/class.divi-coming-soon.core.php' );

add_action( 'init', array( 'DiviComingSoon', 'init' ) );
	
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	
	require_once( DIVI_COMINGSOON_PLUGIN_DIR . '/class.divi-coming-soon.admin.core.php' );
	add_action( 'init', array( 'DiviComingSoon_Admin', 'init' ) );
}