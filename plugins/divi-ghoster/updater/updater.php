<?php
/* 
This file contains code from the Easy Digital Downloads Software Licensing addon.
Copyright Easy Digital Downloads; licensed under GPLv2 or later (see license.txt file included with plugin).
Modified by Aspen Grove Studios to customize settings, add additional functionality, etc., 2017-2018.
Further modified by Aspen Grove Studios 2019-01-08 and 2019-01-09 to customize names and/or settings for the Divi Ghoster plugin.
*/

if (!defined('ABSPATH')) exit;

define( 'AGS_GHOSTER_STORE_URL', DiviGhoster::PLUGIN_AUTHOR_URL );
define( 'AGS_GHOSTER_ITEM_NAME', 'Divi Ghoster' ); // Needs to exactly match the download name in EDD
define( 'AGS_GHOSTER_PLUGIN_PAGE', 'admin.php?page=divi_ghoster' );

define('AGS_GHOSTER_BRAND_NAME', 'Aspen Grove Studios');

if( !class_exists( 'AGS_GHOSTER_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

// Load translations
load_plugin_textdomain('aspengrove-updater', false, plugin_basename(dirname(__FILE__).'/lang'));

function AGS_GHOSTER_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'AGS_GHOSTER_license_key' ) );

	// setup the updater
	new AGS_GHOSTER_Plugin_Updater( AGS_GHOSTER_STORE_URL, DiviGhoster::$pluginFile, array(
			'version' 	=> DiviGhoster::PLUGIN_VERSION, // current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => AGS_GHOSTER_ITEM_NAME, 	// name of this plugin
			'author' 	=> AGS_GHOSTER_BRAND_NAME,  // author of this plugin
			'beta'		=> false
		)
	);
	
	// creates our settings in the options table
	register_setting('AGS_GHOSTER_license', 'AGS_GHOSTER_license_key', 'AGS_GHOSTER_sanitize_license' );
	
	if (isset($_POST['AGS_GHOSTER_license_key_deactivate'])) {
		require_once(dirname(__FILE__).'/license-key-activation.php');
		$result = AGS_GHOSTER_deactivate_license();
		if ($result !== true) {
			define('AGS_GHOSTER_DEACTIVATE_ERROR', empty($result) ? __('An unknown error has occurred. Please try again.', 'aspengrove-updater') : $result);
		}
		unset($_POST['AGS_GHOSTER_license_key_deactivate']);
	}
}
add_action( 'admin_init', 'AGS_GHOSTER_updater', 0 );


function AGS_GHOSTER_has_license_key() {
	return (get_option('AGS_GHOSTER_license_status') === 'valid');
}

function AGS_GHOSTER_activate_page() {
	$license = get_option( 'AGS_GHOSTER_license_key' );
	$status  = get_option( 'AGS_GHOSTER_license_status' );
	?>
		<div class="wrap" id="AGS_GHOSTER_license_key_activation_page">
			<form method="post" action="options.php" id="AGS_GHOSTER_license_key_form">
				<div id="AGS_GHOSTER_license_key_form_logo_container">
					<a href="https://aspengrovestudios.com/?utm_source=<?php echo(DiviGhoster::PLUGIN_SLUG); ?>&amp;utm_medium=plugin-credit-link&amp;utm_content=license-key-activate" target="_blank">
						<img src="<?php echo(plugins_url('logo.png', __FILE__)); ?>" alt="<?php echo(AGS_GHOSTER_BRAND_NAME); ?>" />
					</a>
				</div>
				
				<div id="AGS_GHOSTER_license_key_form_body">
					<div id="AGS_GHOSTER_license_key_form_title">
						<?php echo(esc_html(AGS_GHOSTER_ITEM_NAME)); ?>
						<small>v<?php echo(DiviGhoster::PLUGIN_VERSION); ?></small>
					</div>
					
					<p>
						Thank you for purchasing <?php echo(htmlspecialchars(AGS_GHOSTER_ITEM_NAME)); ?>!<br />
						Please enter your license key below.
					</p>
					
					<?php settings_fields('AGS_GHOSTER_license'); ?>
					
					<label>
						<span><?php _e('License Key:', 'aspengrove-updater'); ?></span>
						<input name="AGS_GHOSTER_license_key" type="text" class="regular-text"<?php if (!empty($_GET['license_key'])) { ?> value="<?php echo(esc_attr($_GET['license_key'])); ?>"<?php } else if (!empty($license)) { ?> value="<?php echo(esc_attr($license)); ?>"<?php } ?> />
					</label>
					
					<?php
						if (isset($_GET['sl_activation']) && $_GET['sl_activation'] == 'false') {
							echo('<p id="AGS_GHOSTER_license_key_form_error">'.(empty($_GET['sl_message']) ? esc_html__('An unknown error has occurred. Please try again.', 'aspengrove-updater') : esc_html($_GET['sl_message'])).'</p>');
						} else if (defined('AGS_GHOSTER_DEACTIVATE_ERROR')) {
							// AGS_GHOSTER_DEACTIVATE_ERROR is already HTML escaped
							echo('<p id="AGS_GHOSTER_license_key_form_error">'.AGS_GHOSTER_DEACTIVATE_ERROR.'</p>');
						}
						
						submit_button('Continue');
					?>
				</div>
			</form>
		</div>
	<?php
}

function AGS_GHOSTER_license_key_box() {
	$status  = get_option( 'AGS_GHOSTER_license_status' );
	?>
		<div id="AGS_GHOSTER_license_key_box">
			<form method="post" action="<?php echo(esc_url(AGS_GHOSTER_PLUGIN_PAGE)); ?>" id="AGS_GHOSTER_license_key_form">
				<div id="AGS_GHOSTER_license_key_form_logo_container">
					<a href="https://aspengrovestudios.com/?utm_source=<?php echo(DiviGhoster::PLUGIN_SLUG); ?>&amp;utm_medium=plugin-credit-link&amp;utm_content=license-key-status" target="_blank">
						<img src="<?php echo(plugins_url('logo.png', __FILE__)); ?>" alt="<?php echo(AGS_GHOSTER_BRAND_NAME); ?>" />
					</a>
				</div>
				
				<div id="AGS_GHOSTER_license_key_form_body">
					<div id="AGS_GHOSTER_license_key_form_title">
						<?php echo(esc_html(AGS_GHOSTER_ITEM_NAME)); ?>
						<small>v<?php echo(DiviGhoster::PLUGIN_VERSION); ?></small>
					</div>
					
					<label>
						<span><?php esc_html_e('License Key:', 'aspengrove-updater'); ?></span>
						<input type="text" readonly="readonly" value="<?php echo(esc_html(get_option('AGS_GHOSTER_license_key'))); ?>" />
					</label>
					
					<?php
						if (defined('AGS_GHOSTER_DEACTIVATE_ERROR')) {
							echo('<p id="AGS_GHOSTER_license_key_form_error">'.AGS_GHOSTER_DEACTIVATE_ERROR.'</p>');
						}
						wp_nonce_field( 'AGS_GHOSTER_license_key_deactivate', 'AGS_GHOSTER_license_key_deactivate' );
						submit_button('Deactivate License Key', '');
					?>
				</div>
			</form>
		</div>
	<?php
}

function AGS_GHOSTER_sanitize_license( $new ) {
	if (defined('AGS_GHOSTER_LICENSE_KEY_VALIDATED')) {
		return $new;
	}
	$old = get_option( 'AGS_GHOSTER_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'AGS_GHOSTER_license_status' ); // new license has been entered, so must reactivate
	}
	
	// Need to activate license here, only if submitted
	require_once(dirname(__FILE__).'/license-key-activation.php');
	AGS_GHOSTER_activate_license($new); // Always redirects
}