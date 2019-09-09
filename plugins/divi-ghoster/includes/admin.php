<?php
/*
Changes by Aspen Grove Studios:
2019-01-02	Created class, partially using code from other Ghoster files and AGS Layouts class template
2019-01-03	Further refactoring of copied Ghoster code, etc.
2019-01-08	Change recommended branding image size; move license key box
*/

class DiviGhosterAdmin {
	
	public static function setup() {
		add_action('admin_init', array('DiviGhosterAdmin', 'onAdminInit'));
		add_action('admin_enqueue_scripts', array('DiviGhosterAdmin', 'adminScripts'));
	}
	
	public static function onAdminInit() {
		register_setting('agsdg_pluginPage', 'agsdg_settings', array('DiviGhosterAdmin', 'sanitizeSettings'));
		
		add_settings_section('agsdg_pluginPage_section_branding_name', __('<hr/>', 'ags-ghoster'), array('DiviGhosterAdmin', 'brandingNameSection'), 'agsdg_pluginPage');
		add_settings_field('agsdg_branding_name', '<label class="agsdg_settings_label">'.__('Enter Branding Name<br />(example: Acme Web Designs)', 'ags-ghoster').'</label>', array('DiviGhosterAdmin', 'brandingNameField'), 'agsdg_pluginPage', 'agsdg_pluginPage_section_branding_name');
		
		add_settings_section('agsdg_pluginPage_section_branding_image', __('<hr/>', 'ags-ghoster'), array('DiviGhosterAdmin', 'brandingImageSection'), 'agsdg_pluginPage');
		add_settings_field('agsdg_branding_image', '<label class="agsdg_settings_label">'.__('Enter Branding Image<br />(minimum 50px by 50px)', 'ags-ghoster').'</label>', array('DiviGhosterAdmin', 'brandingImageField'), 'agsdg_pluginPage', 'agsdg_pluginPage_section_branding_image');
		
		add_settings_section('agsdg_pluginPage_section_theme_slug', __('<hr/>', 'ags-ghoster'), array('DiviGhosterAdmin', 'themeSlugSection'), 'agsdg_pluginPage');
		add_settings_field('agsdg_theme_slug', '<label class="agsdg_settings_label">'.__('Enter Slug Text (example: acme_web_designs)', 'ags-ghoster').'</label>', array('DiviGhosterAdmin', 'themeSlugField'), 'agsdg_pluginPage', 'agsdg_pluginPage_section_theme_slug');
		
		add_settings_section('agsdg_pluginPage_section_ultimate_ghoster', __('<hr/>', 'ags-ghoster'), array('DiviGhosterAdmin', 'ultimateGhosterSection'), 'agsdg_pluginPage');
		add_settings_field('agsdg_ultimate_ghoster', '<label class="agsdg_settings_label">'.__('Ultimate Ghoster', 'ags-ghoster').'</label>', array('DiviGhosterAdmin', 'ultimateGhosterField'), 'agsdg_pluginPage', 'agsdg_pluginPage_section_ultimate_ghoster');
		
	}
	
	public static function menuPage() {
		(AGS_GHOSTER_has_license_key() ? self::adminPage() : AGS_GHOSTER_activate_page());
	}
	
	public static function adminScripts($hook) {
		if ($hook == 'toplevel_page_divi_ghoster') {
			wp_enqueue_media();
			wp_enqueue_script('agsdg_admin_page', DiviGhoster::$pluginBaseUrl.'js/admin-page.js', array('jquery'), '1.1', true);
			wp_enqueue_script('agsdg_jq_checkbox', DiviGhoster::$pluginBaseUrl.'js/jquery.checkbox.min.js', array('jquery'), false, true);
			wp_enqueue_style('agsdg_jq_checkbox', DiviGhoster::$pluginBaseUrl.'css/jquery.checkbox.css');
		}
		wp_enqueue_style('dg_admin', DiviGhoster::$pluginBaseUrl.'css/admin.css');
	}
	

	public static function brandingNameField($args) {
		?><input type="text" size="45" name="agsdg_settings[branding_name]" value="<?php echo htmlspecialchars(DiviGhoster::$settings['branding_name']); ?>" class="agsdg_settings_field"><?php
	}
	
	public static function brandingNameSection() {
		echo '<span class="agsdg_settings_section_heading">'.__('Branding Name', 'ags-ghoster').'</span>';
	}
	
	public static function brandingImageField() {
		?>
		<input id="image-url" type="text" name="agsdg_settings[branding_image]" size="45" value="<?php echo htmlspecialchars(DiviGhoster::$settings['branding_image']); ?>" class="agsdg_settings_field" />
		<strong style='font-weight:600;'>&nbsp;Or&nbsp;</strong>
		<input id="upload-button" type="button" class="button dg-button" value="Upload Image" />
		<?php
	}
	
	public static function brandingImageSection() {
		echo '<span class="agsdg_settings_section_heading">'.__('Branding Image', 'ags-ghoster').'</span>';
	}

	public static function themeSlugField() {
		?><input type="text" size="45" name="agsdg_settings[theme_slug]" value="<?php echo htmlspecialchars(DiviGhoster::$settings['theme_slug']); ?>" class="agsdg_settings_field"><?php
	}
	
	public static function themeSlugSection() {
		echo '<span class="agsdg_settings_section_heading">'.__('Theme URL Slug (see documentation)', 'ags-ghoster').'</span>';
	}

	public static function ultimateGhosterField() {
		?><input name="agsdg_settings[ultimate_ghoster]" type="checkbox"<?php echo(DiviGhoster::$settings['ultimate_ghoster'] == 'yes' ? ' checked="checked"' : ''); ?>><?php
	}

	public static function ultimateGhosterSection() {
		echo '<span class="agsdg_settings_section_heading">'.__('Ultimate Ghoster', 'ags-ghoster').'</span>';
	}

	public static function sanitizeSettings($settings) {
		include_once(__DIR__.'/ultimate.php');
		
		// Make sure Branding Name is set
		if (empty($settings['branding_name'])) {
			add_settings_error('branding_name', 'branding_name_empty', __('The Branding Name field may not be empty.', 'ags-ghoster'));
			$settings['branding_name'] = (empty(DiviGhoster::$settings['branding_name']) ? DiviGhoster::$targetTheme : DiviGhoster::$settings['branding_name']);
		}
		
		// Make sure Theme Slug is set, is not the theme default, and does not contain invalid characters
		if (empty($settings['theme_slug'])) {
			add_settings_error('theme_slug', 'theme_slug_empty', __('The Theme Slug field may not be empty.', 'ags-ghoster'));
			$settings['theme_slug'] = (empty(DiviGhoster::$settings['theme_slug']) ? 'ghost_'.DiviGhoster::$targetThemeSlug : DiviGhoster::$settings['theme_slug']);
		} else if (strcasecmp($settings['theme_slug'], DiviGhoster::$targetThemeSlug) == 0) {
			add_settings_error('theme_slug', 'theme_slug_empty', __('The Theme Slug must be something other than the default', 'ags-ghoster').' &quot;'.DiviGhoster::$targetThemeSlug.'&quot;.');
			$settings['theme_slug'] = (empty(DiviGhoster::$settings['theme_slug']) ? 'ghost_'.DiviGhoster::$targetThemeSlug : DiviGhoster::$settings['theme_slug']);
		} else {
			$newSlug = preg_replace('/[^A-Za-z0-9_\-]+/', '', $settings['theme_slug']);
			if ($newSlug != $settings['theme_slug']) {
				add_settings_error('theme_slug', 'theme_slug_invalid_chars', __('The theme slug may only contain letters, numbers, dashes, and underscores.', 'ags-ghoster'));
				$settings['theme_slug'] = $newSlug;
			}
		}
		
		// Handle Ultimate Ghoster setting
		if (!empty($settings['ultimate_ghoster'])) {
			$settings['ultimate_ghoster'] = 'no';
			
			if (get_option('permalink_structure', '') == '') {
				add_settings_error('ultimate_ghoster', 'ultimate_ghoster_plain_permalinks', __('Ultimate Ghoster cannot be enabled while your permalink structure is set to Plain. Please change your permalink structure in Settings &gt; Permalinks.', 'ags-ghoster'));
			} else if (($settings['theme_slug'] != DiviGhoster::$settings['theme_slug'] || DiviGhoster::$settings['ultimate_ghoster'] != 'yes')) {
				try {
					DiviGhosterUltimate::disable(DiviGhoster::$settings['theme_slug']);
				} catch (Exception $ex) {}
				try {
					DiviGhosterUltimate::enable($settings['theme_slug'], $settings['branding_name']);
					$settings['ultimate_ghoster'] = 'yes';
					update_option('adsdg_ultimate_theme', DiviGhoster::$targetTheme);
					add_settings_error('ultimate_ghoster', 'ultimate_ghoster_enabled', __('Settings saved; Ultimate Ghoster is enabled.', 'ags-ghoster'), 'updated');
				} catch (Exception $ex) {
					add_settings_error('ultimate_ghoster', 'ultimate_ghoster_enable_error', __('An error occurred while enabling Ultimate Ghoster; please try again. If the problem persists, you may need to re-install your theme.', 'ags-ghoster'));
				}
			}
		}
		
		if (empty($settings['ultimate_ghoster']) || $settings['ultimate_ghoster'] == 'no') {
			$settings['ultimate_ghoster'] = 'no';
			delete_option('adsdg_ultimate_theme');
			
			if (DiviGhoster::$settings['ultimate_ghoster'] == 'yes') {
				try {
					DiviGhosterUltimate::disable(DiviGhoster::$settings['theme_slug']);
					add_settings_error('ultimate_ghoster', 'ultimate_ghoster_disabled', __('Ultimate Ghoster has been disabled.', 'ags-ghoster'), 'updated');
				} catch (Exception $ex) {
					add_settings_error('ultimate_ghoster', 'ultimate_ghoster_disable_error', __('An error occurred while disabling Ultimate Ghoster. Please try enabling and disabling it again.', 'ags-ghoster'));
				}
			}
		}
		
		return $settings;
	}

	public static function adminPage() {
		?>
		<div id="agsdg_main_div">

		<?php settings_errors(); ?>


		<form action='options.php' method='post'>
			<img id="agsdg_logo" src='<?php echo(esc_url(DiviGhoster::$pluginBaseUrl.'logo.png')); ?>' />
		<?php
			settings_fields('agsdg_pluginPage');
			do_settings_sections('agsdg_pluginPage');
		?>

		<em>If you are using a caching plugin, <strong>be sure to clear its cache</strong> after enabling or disabling Ultimate Ghoster!</em><br />
		<em>Ultimate Ghoster will not work if the permalink structure is set to Plain in Settings &gt; Permalinks.</em><br />
		<em>Enabling Ultimate Ghoster will hide the <?php echo(DiviGhoster::$targetTheme); ?> Ghoster plugin. Please copy this URL and save it to disable this feature later: <a href="<?php
			$ghosterUrl = admin_url('admin.php?page=divi_ghoster'); echo($ghosterUrl);
		?> "><?php
			echo($ghosterUrl);
		?></a></em>
		<br/>
		<?php if (DiviGhoster::$targetTheme == 'Divi') { ?>
		<em>Enabling Ultimate Ghoster will hide &quot;Divi Switch&quot;, &quot;Divi Booster&quot;, and &quot;Aspen Footer Editor&quot; from the Divi menu and the Plugins list. If installed, they can be accessed directly at any time by visiting:
		<br />For Divi Switch: <a href="<?php echo admin_url('admin.php?page=divi-switch-settings'); ?> "><?php echo admin_url('admin.php?page=divi-switch-settings'); ?></a>
		<br />For Divi Booster: <a href="<?php echo admin_url('admin.php?page=wtfdivi_settings'); ?> "><?php echo admin_url('admin.php?page=wtfdivi_settings'); ?></a>
		<br />For Aspen Footer Editor: <a href="<?php echo admin_url('admin.php?page=aspen-footer-editor'); ?> "><?php echo admin_url('admin.php?page=aspen-footer-editor'); ?></a>
		</em>
		<?php } else if (DiviGhoster::$targetTheme == 'Extra') { ?>
		<em>Enabling Ultimate Ghoster will hide &quot;Divi Switch&quot;, &quot;Divi Booster&quot;, and &quot;Aspen Footer Editor&quot; from the Divi menu and the Plugins list. If installed, they can be accessed directly at any time by visiting:
		<br />For Divi Switch: <a href="<?php echo admin_url('admin.php?page=divi-switch-settings'); ?> "><?php echo admin_url('admin.php?page=divi-switch-settings'); ?></a>
		<br />For Divi Booster: <a href="<?php echo admin_url('admin.php?page=wtfdivi_settings'); ?> "><?php echo admin_url('admin.php?page=wtfdivi_settings'); ?></a>
		<br />For Aspen Footer Editor: <a href="<?php echo admin_url('admin.php?page=aspen-footer-editor'); ?> "><?php echo admin_url('admin.php?page=aspen-footer-editor'); ?></a>
		</em>
		<?php } ?>
		<br /><br /><br />
		<button id='epanel-save-top' class='save-button button dg-button' name="btnSubmit">Save Changes</button>
		<br/><br/>
		<hr/>
		<br/>
		<p class="branding_name">Customize the standard WordPress login page with your own logo, background, and colors with an easy to use interface.</p>
		<br/>
		<a class="button button-primary" href="<?php echo admin_url('customize.php?et_customizer_option_set=theme'); ?>">Login Customizer</a>
		</form>
		<?php AGS_GHOSTER_license_key_box(); ?>
		</div>
		<?php
	}
}

DiviGhosterAdmin::setup();