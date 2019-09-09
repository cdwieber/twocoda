<?php
/*
Plugin Name: Divi Ghoster
Plugin URI: https://aspengrovestudios.com/product/divi-ghoster/
Description: White label Divi and Extra with your own brand.
Version: 2.2.0
Author: Aspen Grove Studios
Author URI: http://aspengrovestudios.com/
License: GPLv2
*/
// IMPORTANT TODO IN FUNCTIONS.PHP

/*

Divi Ghoster plugin
Copyright (C) 2019 Aspen Grove Studios

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

========

Credits:

This plugin includes code copied from and/or based on parts of the Divi
theme and/or the Divi Builder by Elegant Themes, licensed GPLv2 (see
license.txt file in the plugin's root directory).

This plugin includes code copied from and/or based on parts of WordPress
by Automattic, licensed GPLv2+ (see license.txt file in the plugin's
root directory for GPLv2).

=======

Note:

Divi is a registered trademark of Elegant Themes, Inc. This product is
not affiliated with nor endorsed by Elegant Themes.

*/

/*
Modified by Aspen Grove Studios:
- 2019-01-02: refactor code to move into separate classes
- 2019-01-03: create DiviGhoster class; refactor code; add/update license details
- 2019-01-04: added exception handling when disabling Ultimate on plugin (de)activation; move admin-filters loading out of is_admin check; update plugin version
- 2019-01-08: Add multisite check; add PLUGIN_AUTHOR_URL, PLUGIN_SLUG constants and $pluginFile variable to DiviGhoster class; rename DiviGhoster::VERSION to DiviGhoster::PLUGIN_VERSION; move updater include
- 2019-01-10: Copy DiviGhosterUltimate::onThemeSetup method (with partial body) to DiviGhoster class along with associated after_setup_theme add_filter call (change add_filter to add_action)
*/

if (!defined('ABSPATH'))
	die();

//require dirname(__FILE__).'/includes/functions.php';


if (!is_multisite()) {

//class AGSLayouts {
class DiviGhoster {

	const PLUGIN_VERSION = '2.2.0', PLUGIN_SLUG = 'divi-ghoster', PLUGIN_AUTHOR_URL = 'https://aspengrovestudios.com/';
	public static $pluginFile, $pluginBaseUrl, $pluginDirectory, $settings, $supportedThemes, $targetTheme, $targetThemeSlug;
	
	public static function run() {
		self::$pluginFile = __FILE__;
		self::$pluginBaseUrl = plugin_dir_url(__FILE__);
		self::$pluginDirectory = __DIR__.'/';
		add_filter('et_pb_load_roles_admin_hook', array('DiviGhoster', 'filterRoleEditorHook'), 9999);
		add_filter('et_divi_role_editor_page', array('DiviGhoster', 'filterRoleEditorPage'), 9999);
		
		add_action('after_setup_theme', array('DiviGhoster', 'onThemeSetUp'), 9999);
		
		include_once(ABSPATH . '/wp-admin/includes/plugin.php');

		self::$supportedThemes = array('Divi', 'Extra');
		self::$targetTheme = wp_get_theme()->get_template();
		if (!in_array(self::$targetTheme, self::$supportedThemes)) {
			self::$targetTheme = 'Divi';
		}
		self::$targetThemeSlug = strtolower(self::$targetTheme);
		
		self::$settings = get_option('agsdg_settings');
		if (self::$settings == false) {
			self::initializeSettings();
		}

		include_once(__DIR__.'/includes/custom-login.php');

		if (is_admin()) {
			include_once(__DIR__.'/includes/admin.php');
		}
		
		include_once(__DIR__.'/includes/admin-filters.php');

		if (self::$settings['ultimate_ghoster'] == 'yes') {
			include_once(__DIR__.'/includes/ultimate.php');
			DiviGhosterUltimate::run();
		}
	}
	
	public static function initializeSettings() {
		$oldOptions = get_option('dwl_settings');
		self::$settings = array(
			'branding_name' => (empty($oldOptions['dwl_text_field_0']) ? self::$targetTheme : $oldOptions['dwl_text_field_0']),
			'branding_image' => get_option('dash_icon_path', ''),
			'theme_slug' => get_option('curr_page', 'ghost_divi'),
			'ultimate_ghoster' => get_option('hidden_stat', 'no')
		);
		self::saveSettings();
	}
	
	public static function saveSettings() {
		update_option('agsdg_settings', self::$settings);
	}

	public static function filterRoleEditorHook($hook) {
		return get_plugin_page_hookname('et_' . self::$settings['theme_slug'] . '_role_editor', 'et_' . self::$settings['theme_slug'] . '_options');
	}
	
	public static function filterRoleEditorPage($page) {
		return 'et_' . self::$settings['theme_slug'] . '_role_editor';
	}
	
	public static function onPluginIsActiveChange() {
		// Update settings
		self::$settings['ultimate_ghoster'] = 'no';
		self::saveSettings();
		delete_option('adsdg_ultimate_theme');
		
		try {
			include_once(__DIR__.'/includes/ultimate.php');
			DiviGhosterUltimate::disable(self::$settings['theme_slug']);
		} catch (Exception $ex) {}
	}
	
	public static function onThemeSetUp() {
		global $themename;
		$themename = DiviGhoster::$settings['branding_name'];
	}
}

require dirname(__FILE__).'/updater/updater.php';

DiviGhoster::run();

// Disable Ultimate Ghoster on plugin activation/deactivation
register_activation_hook(__FILE__, array('DiviGhoster', 'onPluginIsActiveChange'));
register_deactivation_hook(__FILE__, array('DiviGhoster', 'onPluginIsActiveChange'));

}