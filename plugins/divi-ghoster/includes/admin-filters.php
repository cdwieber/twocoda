<?php
/*
This file includes code copied from and/or based on parts of the Divi
theme and/or the Divi Builder by Elegant Themes, licensed GPLv2 (see
../license.txt file for license text).

Changes by Aspen Grove Studios:
2019-01-02	Created class, partially using code from other Ghoster files and AGS Layouts class template
2019-01-03	Further refactoring of copied Ghoster code, etc.
2019-01-04	Further refactoring-related changes; re-write portability export filename replacement code and add hook for backend
2019-01-07	Add display_post_states filter; rename adminCss function to adminCssJs; implement JS text override for "Use The Divi Builder" button; fix theme check at start of adminCssJs function; add CSS and JS to replace Divi icon in Gutenberg integration; add filters and CSS to force BFB backend builder and hide associated options; hide builder help videos
2019-01-08	Add CSS to replace Divi icon in Gutenberg integration
*/

class DiviGhosterAdminFilters {
	
	public static function setup() {
		add_action('admin_bar_menu', array('DiviGhosterAdminFilters', 'filterAdminBarMenu'), 9999);
		add_action('et_fb_enqueue_assets', array('DiviGhosterAdminFilters', 'fbAssets'), 9);
		
		add_filter('option_et_bfb_settings', array('DiviGhosterAdminFilters', 'filterWpOption'), 10, 2);
		add_filter('et_fb_help_videos', array('DiviGhosterAdminFilters', 'filterBuilderHelpVideos'));
		
		if (is_admin()) {
			add_action('admin_menu', array('DiviGhosterAdminFilters', 'modifyAdminMenu'), 9999);
			add_action('admin_head', array('DiviGhosterAdminFilters', 'adminCssJs'));
			
			add_action('et_pb_before_page_builder', array('DiviGhosterAdminFilters', 'builderScript'));
			
			add_filter('display_post_states', array('DiviGhosterAdminFilters', 'filterPostStates'), 9999);
			add_filter('et_builder_settings_definitions', array('DiviGhosterAdminFilters', 'filterThemeOptionsDefinitionsBuilder'));
			self::addTranslateFilters();
		
			// Remove the translate filters before the plugins list (will be restored after the plugins list)
			global $pagenow;
			if ((isset($pagenow) && $pagenow == 'plugins.php')) {
				add_action('load-plugins.php', array('DiviGhosterAdminFilters', 'removeTranslateFilters'));
				add_action('admin_enqueue_scripts', array('DiviGhosterAdminFilters', 'addTranslateFilters'));
			}
			// Show portability button on Theme Options page
			if (isset($_GET['page']) && $_GET['page'] == 'et_'.DiviGhoster::$settings['theme_slug'].'_options') {
				add_filter('et_core_portability_args_epanel', array('DiviGhosterAdminFilters', 'filterPortabilityArgs'));
			}
			
		}
		
	}
	
	public static function fbAssets() {
		// "Translate" frontend builder strings
		if (has_action('et_fb_enqueue_assets', 'et_fb_backend_helpers') && function_exists('et_fb_backend_helpers')) {
			remove_action('et_fb_enqueue_assets', 'et_fb_backend_helpers');
			add_filter('gettext', array('DiviGhosterAdminFilters', 'filterTranslatedTextDiviOnly'));
			add_filter('ngettext', array('DiviGhosterAdminFilters', 'filterTranslatedTextDiviOnly'));
			et_fb_backend_helpers();
			remove_filter('gettext', array('DiviGhosterAdminFilters', 'filterTranslatedTextDiviOnly'));
			remove_filter('ngettext', array('DiviGhosterAdminFilters', 'filterTranslatedTextDiviOnly'));
		}
		add_action('wp_footer', array('DiviGhosterAdminFilters', 'builderScript'), 999);
	}
	
	public static function builderScript() {
		// Script to reset the export file name
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Following code from WP and Divi Icons Pro (fb.js) - modified
				
				var MO = window.MutationObserver ? window.MutationObserver : window.WebkitMutationObserver;
				var fbApp = document.getElementById('et-fb-app');
				if (!fbApp) {
					fbApp = document.body;
				}
				
				if (MO && fbApp) {
					(new MO(function(events) {
						$.each(events, function(i, event) {
							if (event.addedNodes && event.addedNodes.length) {
								$.each(event.addedNodes, function(i, node) {
									var $exportFileNameField = $(node).find('#et-fb-exportFileName');
									if ($exportFileNameField.length) {
										$exportFileNameField.val($exportFileNameField.val().replace('<?php echo(addslashes(DiviGhoster::$targetTheme)); ?>', '<?php echo(addslashes(DiviGhoster::$settings['branding_name'])); ?>'));
									}
								});
							}
						});
					})).observe(fbApp, {childList: true, subtree: true});
				}
				
				// End code from WP and Divi Icons Pro
			});
		</script>
		<?php
	}
	
	public static function filterAdminBarMenu($admin_bar) {
		$visualBuilderNode = $admin_bar->get_node('et-use-visual-builder');
		if (!empty($visualBuilderNode)) {
			$admin_bar->remove_node('et-use-visual-builder');
			$visualBuilderNode = get_object_vars($visualBuilderNode);
			$visualBuilderNode['id'] = 'agsdg-et-use-visual-builder';
			$admin_bar->add_node($visualBuilderNode);
		}
	}
	
	public static function filterPortabilityArgs($args) {
		$args->view = true;
		return $args;
	}
	
	public static function addTranslateFilters() {	
		add_filter('gettext', array('DiviGhosterAdminFilters', 'filterTranslatedText'));
		add_filter('ngettext', array('DiviGhosterAdminFilters', 'filterTranslatedText'));
	}
	
	public static function removeTranslateFilters() {
		remove_filter('gettext', array('DiviGhosterAdminFilters', 'filterTranslatedText'));
		remove_filter('ngettext', array('DiviGhosterAdminFilters', 'filterTranslatedText'));
	}
	
	public static function modifyAdminMenu() {
		global $menu, $submenu, $pagenow, $plugin_page;
		
		// Add Ghoster menu item
		if (DiviGhoster::$settings['ultimate_ghoster'] != 'yes' || ($pagenow == 'admin.php' && $plugin_page == 'divi_ghoster')) {
			add_menu_page(DiviGhoster::$targetTheme.' Ghoster', DiviGhoster::$targetTheme.' Ghoster', 'manage_options', 'divi_ghoster', array('DiviGhosterAdmin', 'menuPage'));
		}
		
		if (!isset($submenu['et_'.DiviGhoster::$targetThemeSlug.'_options']))
			return;
		
		foreach ($menu as $menuItem) {
			if ($menuItem[2] == 'et_'.DiviGhoster::$targetThemeSlug.'_options') {
				$menuExists = true;
				break;
			}
		}
		if (empty($menuExists)) {
			return;
		}
		
		global $_wp_submenu_nopriv, $_parent_pages, $_registered_pages, $wp_filter;
		
		// Remove certain menu items if Ultimate Ghoster is enabled
		if (DiviGhoster::$settings['ultimate_ghoster'] == 'yes') {
			
			switch (DiviGhoster::$targetTheme) {
				case 'Divi':
					// Remove Divi Switch
					if ($pagenow != 'admin.php' || $plugin_page != 'divi-switch-settings') {
						remove_submenu_page('et_divi_options', 'admin.php?page=divi-switch-settings');
					}
					// Remove Divi Booster
					if ($pagenow != 'admin.php' || $plugin_page != 'wtfdivi_settings') {
						remove_submenu_page('et_divi_options', 'wtfdivi_settings');
					}
					// Remove Aspen Footer Editor
					if ($pagenow != 'admin.php' || $plugin_page != 'aspen-footer-editor') {
						remove_submenu_page('et_divi_options', 'aspen-footer-editor');
					}
					break;
				case 'Extra':
					// Remove Divi Switch
					if ($pagenow != 'admin.php' || $plugin_page != 'divi-switch-settings') {
						remove_submenu_page('et_divi_options', 'admin.php?page=divi-switch-settings');
					}
					// Remove Divi Booster
					if ($pagenow != 'admin.php' || $plugin_page != 'wtfdivi_settings') {
						remove_submenu_page('et_divi_options', 'wtfdivi_settings');
					}
					// Remove Aspen Footer Editor
					if ($pagenow != 'admin.php' || $plugin_page != 'aspen-footer-editor') {
						remove_submenu_page('et_divi_options', 'aspen-footer-editor');
					}
					break;
			}
			
		}
		
		$menuItems = array(
			'et_'.DiviGhoster::$targetThemeSlug.'_options' => array(
				'slug' => 'et_' . DiviGhoster::$settings['theme_slug'] . '_options',
				'name' => DiviGhoster::$settings['branding_name']
			),
			/*'et_divi_100_options' => array(
				'slug' => 'et_' . $settings['theme_slug'] . '_addons_options',
				'name' => $settings['branding_name'].' Addons'
			)*/
		);
		
		// Temporary - for release
		foreach ($menu as $i => $menuItem) {
			if ($menuItem[2] == 'et_divi_100_options') {
				$menu[$i][0] = DiviGhoster::$settings['branding_name'].' Addons';
				$menu[$i][3] = DiviGhoster::$settings['branding_name'].' Addons';
				break;
			}
		}
		
		foreach ($menuItems as $oldSlug => $params) {
			
			// Add top-level theme pages and copy hooked functions from the old page hooks to the new ones
			$hookName = add_menu_page($params['name'], $params['name'], 'switch_themes', $params['slug']);
			$oldHookName = get_plugin_page_hookname($oldSlug, '');
			foreach (array('', 'load-', 'admin_print_scripts-', 'admin_head-') as $prefix) {
				if (!empty($wp_filter[$prefix.$oldHookName])) {
					foreach ($wp_filter[$prefix.$oldHookName] as $priority => $hooks) {
						foreach ($hooks as $hook) {
							add_action($prefix.$hookName, $hook['function'], $priority, $hook['accepted_args']);
						}
					}
				}
			}
			
			
			// Copy submenu items
			$newSlug = plugin_basename($params['slug']);
			$submenu[$newSlug] = $submenu[$oldSlug];
			foreach ($submenu[$newSlug] as $i => $subMenuItem) {
				
				$oldSubmenuSlug = $submenu[$newSlug][$i][2];
				if ($oldSubmenuSlug == $oldSlug) {
					$submenu[$newSlug][$i][2] = $params['slug'];
				} else if ($oldSubmenuSlug == 'et_'.DiviGhoster::$targetThemeSlug.'_role_editor') {
					$submenu[$newSlug][$i][2] = 'et_' . DiviGhoster::$settings['theme_slug'] . '_role_editor';
				}
				
				// Copy hooked functions from the old submenu page hooks to the new ones
				$oldHookName = get_plugin_page_hookname($oldSubmenuSlug, $oldSlug);
				$hookName = get_plugin_page_hookname($submenu[$newSlug][$i][2], $params['slug']);
				foreach (array('', 'load-', 'admin_print_scripts-', 'admin_head-') as $prefix) {
					if (!empty($wp_filter[$prefix.$oldHookName])) {
						foreach ($wp_filter[$prefix.$oldHookName] as $priority => $hooks) {
							foreach ($hooks as $hook) {
								add_action($prefix.$hookName, $hook['function'], $priority, $hook['accepted_args']);
							}
						}
					}
				}
				
				unset($_registered_pages[$oldHookName]);
				$_registered_pages[$hookName] = 1;
				
				$_parent_pages[$submenu[$newSlug][$i][2]] = $newSlug;
				
			}
			if (isset($_wp_submenu_nopriv[$oldSlug]))
				$_wp_submenu_nopriv[$newSlug] = $_wp_submenu_nopriv[$oldSlug];
			
			remove_menu_page($oldSlug);
			unset($submenu[$oldSlug]);
			
		}
			
	}
		
	public static function filterTranslatedText($translated) {
		return empty(DiviGhoster::$targetTheme) ? $translated : preg_replace((DiviGhoster::$targetTheme == 'Divi' ? '/(Divi\b)/' : '/('.DiviGhoster::$targetTheme.'\b|Divi\b)/'), DiviGhoster::$settings['branding_name'], $translated);
	}
	
	public static function filterTranslatedTextDiviOnly($text) {
		return str_replace('Divi', DiviGhoster::$settings['branding_name'], $text);
	}

	public static function adminCssJs() {
		// Do not output the admin CSS if the currently active theme is not the one that Ghoster has been applied to
		$currentTemplate = wp_get_theme()->get_template();
		if ($currentTemplate != DiviGhoster::$targetTheme && $currentTemplate != DiviGhoster::$settings['theme_slug']) {
			return;
		}

		echo('<style>');
		
		if (!empty(DiviGhoster::$settings['branding_image'])) {
		?>

			#adminmenu #toplevel_page_et_<?php echo DiviGhoster::$settings['theme_slug']; ?>_options div.wp-menu-image::before,
			#adminmenu #toplevel_page_et_divi_100_options div.wp-menu-image::before {
			background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat !important;
			content:'' !important;
			margin-top: 6px !important;
			max-width:22px !important;
			max-height:22px !important;
			width: 100%;
			background-size: contain!important;
		}
			#et_pb_layout .hndle:before, #et_pb_toggle_builder:before
			{
				color:transparent !important;
				background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat  !important;
				background-size: contain!important;
				max-height: 33px;
				max-width: 36px;
				width: 100%;
			}
			
			#et_pb_layout h3:before
			{
			background-image: url(<?php
				echo DiviGhoster::$settings['branding_image'];
		?>) no-repeat  !important;
			}
			#et_settings_meta_box .hndle.ui-sortable-handle::before{
			color:transparent !important;
			background: url(<?php
				echo DiviGhoster::$settings['branding_image'];
		?>) no-repeat !important;	
			max-height: 26px;
			max-width: 26px;
			margin: 9px 0px 0px 0px;
			background-size: contain!important;
			}
		#et_settings_meta_box .hndle:before
		{
			color:transparent !important;
			background: url(<?php
				echo DiviGhoster::$settings['branding_image'];
		?>) no-repeat !important;	
			height:36px;
			width:36px;
			margin:6px 0px 0px 0px;
		}
		#epanel-logo{
			content: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) !important;
			width:143px; 
			height:65px;
		}
		#epanel-title {
			background-color: transparent !important;
		}
		#epanel-title:before {
			display: none;
		}
		#epanel-header:before {
			display: block;
			float: left;
			vertical-align: top;
			background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat !important;
			content: '' !important;
			width: 32px !important;
			height: 32px !important;
			margin-top: -4px;
			margin-right: 10px;
			background-size: contain !important;
			background-position: left 0px center !important;
		}
		
		.divi-ghoster-placeholder-block-icon {
			background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat;
			background-size: contain;
			background-position: left 0px center;
		}
		
		.wp-block-divi-placeholder .et-icon:before {
			background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat;
			content: '' !important;
			width: 50px;
			height: 50px;
			margin-left: auto;
			margin-right:auto;
			background-size: contain;
			background-position: left 0px center;
		}
		
		.editor-post-switch-to-divi:after {
			background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat;
			content: '' !important;
			width: 32px;
			height: 32px;
			margin-top: -4px;
			margin-left: -5px;
			background-size: contain;
			background-position: left 0px center;
		}
		
		.et_pb_roles_title:before {
			background: url(<?php echo DiviGhoster::$settings['branding_image']; ?>) no-repeat !important;
			content: '' !important;
			width: 32px !important;
			height: 32px !important;
			background-size: contain !important;
			background-position: left 0px center !important;
		}
		<?php } // /!empty(DiviGhoster::$settings['branding_image'])
		if (DiviGhoster::$settings['ultimate_ghoster'] == 'yes') { // Output CSS to hide the duplicate theme in the theme editor dropdown ?>
		body.theme-editor-php #theme option[value="<?php echo DiviGhoster::$settings['theme_slug']; ?>"],.et-bfb-optin-cta {
			display: none;
		}
		<?php } // /ultimate_ghoster == 'yes' ?>
		</style>
		
		<?php if ($GLOBALS['pagenow'] == 'post.php') { ?>
		<script type="text/javascript">
			
			/*
			This script includes code copied from and/or based on parts of the Divi
			theme and/or the Divi Builder by Elegant Themes, licensed GPLv2 (see
			<?php echo(DiviGhoster::$pluginBaseUrl); ?>license.txt file for license text).
			*/
		
			jQuery(document).ready(function($) {
				// Following code from WP and Divi Icons Pro (fb.js) - modified
				
				var MO = window.MutationObserver ? window.MutationObserver : window.WebkitMutationObserver;
				var editor = document.getElementById('editor');
				if (MO && editor) {
					(new MO(function(events) {
						$.each(events, function(i, event) {
							if (event.addedNodes && event.addedNodes.length) {
								var $newElements = $(event.addedNodes);
								$newElements.find('.editor-post-switch-to-divi').addBack('.editor-post-switch-to-divi').each(function() {
									var $button = $(this);
									$button.html($button.html().replace('Divi', '<?php echo(addslashes(DiviGhoster::$settings['branding_name'])); ?>'));
								});
								$newElements.find('.dashicons-format-image').addBack('.dashicons-format-image').each(function() {
									var $icon = $(this);
									/* Contents of the attribute selector brackets in the following line were copied from Divi by Elegant Themes, modified by Aspen Grove Studios 2019-01-07 to remove newlines and some whitespace - includes/builder/frontend-builder/gutenberg/blocks/placeholder/placeholder.js */
									if ($icon.has('path[d="M7.5,6H7v4h0.5c2.125,0,2.125-1.453,2.125-2C9.625,7.506,9.625,6,7.5,6z M8,3C5.239,3,3,5.239,3,8 c0,2.761,2.239,5,5,5s5-2.239,5-5C13,5.239,10.761,3,8,3z M7.5,11h-1C6.224,11,6,10.761,6,10.467V5.533C6,5.239,6.224,5,6.5,5 c0,0,0.758,0,1,0c1.241,0,3.125,0.51,3.125,3C10.625,10.521,8.741,11,7.5,11z"]')) {
										$icon.addClass('divi-ghoster-placeholder-block-icon').empty();
									}
								});
								
							}
						});
					})).observe(editor, {childList: true, subtree: true});
				}
				
				// End code from WP and Divi Icons Pro
			});
		</script>
		<?php } ?>
		
		<?php
	}
	
	public static function filterPostStates($states) {
		$diviIndex = array_search('Divi', $states);
		if ($diviIndex !== false) {
			$states[$diviIndex] = DiviGhoster::$settings['branding_name'];
		}
		return $states;
	}
	
	public static function filterWpOption($optionValue, $optionName) {
		switch ($optionName) {
			case 'et_bfb_settings':
				if (DiviGhoster::$settings['ultimate_ghoster'] == 'yes') {
					$optionValue['enable_bfb'] = 'on';
				}
				break;
		}
		return $optionValue;
	}
	
	public static function filterThemeOptionsDefinitionsBuilder($options) {
		if (DiviGhoster::$settings['ultimate_ghoster'] == 'yes') {
			unset($options['et_enable_bfb']);
		}
		return $options;
	}
	
	public static function filterBuilderHelpVideos() {
		return array();
	}
}

DiviGhosterAdminFilters::setup();