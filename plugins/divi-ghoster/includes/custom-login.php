<?php
/*
Changes by Aspen Grove Studios:
2019-01-02	Created class, partially using code from other Ghoster files and AGS Layouts class template
2019-01-03	Remove unnecessary code; fix incorrect method name
*/

class DiviGhosterCustomLogin {
	
	public static function setup() {
		add_action('customize_register', array('DiviGhosterCustomLogin', 'wp_admin_area_customization'));
		add_action('login_head', array('DiviGhosterCustomLogin', 'get_login_area_bg_image'));
		add_action('login_head', array('DiviGhosterCustomLogin', 'custom_login_logo'));
		add_action('login_head', array('DiviGhosterCustomLogin', 'get_login_area_color'));
		add_action('login_head', array('DiviGhosterCustomLogin', 'get_login_area_alighment'));
		add_action('login_head', array('DiviGhosterCustomLogin', 'load_wp_login_style'));
		
		add_filter('login_headerurl', array('DiviGhosterCustomLogin', 'custom_login_area_logo_image'));
		add_filter('login_headertitle', array('DiviGhosterCustomLogin', 'custom_login_area_logo_image_title'));
	}
	
	public static function wp_admin_area_customization($wp_customize) {
		// Check color control dependency
		if (class_exists('ET_Color_Alpha_Control')) {
			$colorControl = 'ET_Color_Alpha_Control';
		} else if (class_exists('ET_Divi_Customize_Color_Alpha_Control')) {
			$colorControl = 'ET_Divi_Customize_Color_Alpha_Control';
		} else {
			return;
		}

		$wp_customize->add_section('wp_admin_area_custom_settings', array(
			'title' => 'Login Customizer'
		));
		$wp_customize->add_setting('login_area_bg_image', array());
		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'login_area_bg_image', array(
			'label' => __('Background Image', 'agsdg'),
			'section' => 'wp_admin_area_custom_settings'
		)));
		$wp_customize->add_setting('login_form_alignment', array(
			'default' => 'none',
			'transport' => 'refresh'
		));
		$wp_customize->add_control('login_form_alignment', array(
			'label' => 'Login Form Alignment',
			'section' => 'wp_admin_area_custom_settings',
			'placeholder' => 'Align the login logo',
			'default' => 'none',
			'type' => 'radio',
			'choices' => array(
				'left' => 'Left',
				'none' => 'Center',
				'right' => 'Right'
			)
		));
		$wp_customize->add_section('wp_admin_area_custom_settings', array(
			'title' => 'Login Customizer'
		));
		$wp_customize->add_setting('login_area_logo_image', array());
		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'login_area_logo_image', array(
			'label' => __('Login Logo (in place of WP logo)', 'ags-ghoster'),
			'section' => 'wp_admin_area_custom_settings'
		)));
		$wp_customize->add_section('wp_admin_area_custom_settings', array(
			'title' => 'Login Customizer'
		));
		$colors   = array();
		$colors[] = array(
			'slug' => 'login_background_color',
			'default' => '#',
			'label' => __('Background Color', 'ags-ghoster')
		);
		$colors[] = array(
			'slug' => 'content_link_color',
			'default' => '#999',
			'label' => __('Links Color', 'ags-ghoster')
		);
		$colors[] = array(
			'slug' => 'form_background_color',
			'default' => '#ffffff',
			'label' => __('Form Background Color', 'ags-ghoster')
		);
		$colors[] = array(
			'slug' => 'background_color_tint',
			'default' => 'rgba(0, 0, 0, 0)',
			'label' => __('Background Image Tint', 'ags-ghoster')
		);
		$colors[] = array(
			'slug' => 'login_submit_color',
			'default' => '#00a0d2',
			'label' => __('Submit Button Color', 'ags-ghoster')
		);
		
		foreach ($colors as $color) {
			$wp_customize->add_setting($color['slug'], array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options'
			));
			$wp_customize->add_control(new $colorControl($wp_customize, $color['slug'], array(
				'label' => $color['label'],
				'section' => 'wp_admin_area_custom_settings',
				'settings' => $color['slug']
			)));
		}
	}

	public static function get_login_area_bg_image() {
		$default = '';
		$value   = get_theme_mod('login_area_bg_image', $default);
		if ($value !== $default) {
			echo '<style type="text/css"> 
		.login, body, html { background-image: url(" ' . get_theme_mod('login_area_bg_image') . ' ")!important;
		} </style>';
		}
	}
	
	public static function custom_login_logo() {
		$default = '';
		$value   = get_theme_mod('login_area_logo_image', $default);
		if ($value !== $default) {
			echo '<style type="text/css">
		h1 a {
		 background-image: url(" ' . $value . ' ") !important;
		}
	  </style>';
		}
	}
	
	public static function custom_login_area_logo_image() {
		return get_bloginfo('url');
	}
	
	public static function custom_login_area_logo_image_title() {
		return get_bloginfo('name');
	}
	
	public static function get_login_area_alighment() {
		$default = '';
		$value   = get_theme_mod('login_form_alignment', $default);
		if ($value !== $default) {
			echo '<style type="text/css"> 
		#login { float: ' . get_theme_mod('login_form_alignment') . '!important;
		} </style>';
		}
	}
	
	public static function get_login_area_color() {
		echo '<style> .login #backtoblog a, #login form p label, .login #nav a, .login h1 a {
		color: ' . get_option('content_link_color') . '!important; } 
		
		
			.login:before {
				background : ' . get_option('login_background_color') . '!important; }
		.login form {
			background: ' . get_option('form_background_color') . '!important;
		}
		.login:after { 
		background: ' . get_option('background_color_tint') . ' !important;
			
		}
		
		.wp-core-ui .button-primary {
			background: ' . get_option('login_submit_color') . '!important;
		}
		</style>';
	}

	public static function load_wp_login_style() {
		wp_enqueue_style('wp_login_css_dg', plugins_url('/css/custom-login.css', __FILE__), '', '1.1', '');
	}
}

DiviGhosterCustomLogin::setup();