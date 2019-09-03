<?php

/**
 * Modify dashboard.
 *
 * @param $settings
 *
 * @return mixed
 */
function jck_sfr_modify_dashboard( $settings ) {
	if ( ! JCK_SFR_Core_Settings::is_settings_page() ) {
		return $settings;
	}

	unset( $settings['sections']['licence']['fields'][1] );

	// Support link.
	$settings['sections']['support']['fields'][0]['default'] = JCK_SFR_Settings::support_link();

	// Docs link.
	$settings['sections']['support']['fields'][1]['default'] = JCK_SFR_Settings::documentation_link();

	sort( $settings['tabs'] );

	return $settings;
}

add_filter( 'wpsf_register_settings_jck_sfr', 'jck_sfr_modify_dashboard' );