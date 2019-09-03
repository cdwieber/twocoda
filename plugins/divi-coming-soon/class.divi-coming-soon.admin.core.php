<?php

	class DiviComingSoon_Admin {
		
		private static $_show_errors = FALSE;
		private static $initiated = FALSE;
		
		/**
		 * Holds the values to be used in the fields callbacks
		 */
		public static $options;
		
		public static function init() {
			
			if ( ! self::$initiated ) {
				
				self::init_hooks();
			}
		}
		
		
		private static function init_hooks() {
			
			self::$initiated = true;
			
			// Admin styles/scripts
			add_action( 'admin_init', array( 'DiviComingSoon_Admin', 'register_assets' ) );
			add_action( 'admin_enqueue_scripts', array( 'DiviComingSoon_Admin', 'include_assets'), '999');
			
			// Add Search Filter Post Title
			add_filter( 'posts_where', array( 'DiviComingSoon_Admin', 'post_title_like_where' ), 10, 2 );
			
			add_action( 'wp_ajax_nopriv_ajax_dcs_listposts', array( 'DiviComingSoon_Admin', 'get_wp_posts' ) );
			add_action( 'wp_ajax_ajax_dcs_listposts', array( 'DiviComingSoon_Admin', 'get_wp_posts' ) );
			
			// Register menu & settings
			add_action( 'admin_menu', array( 'DiviComingSoon_Admin', 'add_admin_submenu' ) );
			add_action( 'admin_init', array( 'DiviComingSoon_Admin', 'register_divicomingsoon_settings' ) );
		}
		
		
		public static function register_assets( $hook ) {
			
			wp_register_style( 'divi-coming-soon-admin-bootstrap', DIVI_COMINGSOON_PLUGIN_URL . 'assets/css/admin/bootstrap.css', array(), '1.0.0', 'all' );
			wp_register_style( 'divi-coming-soon-select2', DIVI_COMINGSOON_PLUGIN_URL . 'assets/css/admin/select2.min.css', array(), '4.0.6', 'all' );
			wp_register_script( 'divi-coming-soon-select2', DIVI_COMINGSOON_PLUGIN_URL . 'assets/js/admin/select2.full.min.js', array('jquery'), '4.0.6', true );
			wp_register_style( 'divi-coming-soon-select2-bootstrap', DIVI_COMINGSOON_PLUGIN_URL . 'assets/css/admin/select2-bootstrap.min.css', array('divi-coming-soon-admin-bootstrap'), '1.0.0', 'all' );
			
			wp_register_style( 'divi-coming-soon-admin', DIVI_COMINGSOON_PLUGIN_URL . 'assets/css/admin/admin.css', array(), '1.0.0', 'all' );
			wp_register_script( 'divi-coming-soon-admin-functions', DIVI_COMINGSOON_PLUGIN_URL . 'assets/js/admin/admin-functions.js', array( 'jquery', 'divi-coming-soon-select2' ), '1.0.0', true );
		}
		
		
		public static function include_assets( $hook ) {
			
			$screen = $_GET['page'];
			
			if ( $screen != 'divicomingsoon' ) {
				return;
			}
			
			wp_enqueue_style( 'divi-coming-soon-select2' );
			wp_enqueue_style( 'divi-coming-soon-select2-bootstrap' );
			wp_enqueue_script( 'divi-coming-soon-select2' );
			
			wp_enqueue_style( 'divi-coming-soon-admin' );
			wp_enqueue_script( 'divi-coming-soon-admin-functions' );
		}
		
		
		public static function post_title_like_where( $where, $wp_query ) {
			
			global $wpdb;
			
			if ( $post_title_like = $wp_query->get( 'post_title_like' ) ) {
				
				$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( trim( $post_title_like ) ) ) . '%\'';
			}
			
			return $where;
		}
		
		
		public static function get_wp_posts() {
			
			if ( isset( $_POST['q'] ) ) {
			
				$q = stripslashes( $_POST['q'] );
			
			} else {
				
				return;
			}
			
			
			if ( isset( $_POST['page'] ) ) {
				
				$page = (int) $_POST['page'];
				
			} else {
				
				$page = 1;
			}
			
			
			if ( isset( $_POST['json'] ) ) {
				
				$json = (int) $_POST['json'];
				
			} else {
				
				$json = 0;
			}
			
			$data = null;
			
			$posts = array();
			
			$total_count = 0;
			
			$args = array(
				'post_title_like' => $q,
				'post_type' => 'page',
				'post_status' => 'publish',
				'cache_results'  => false,
				'posts_per_page' => 7,
				'paged' => $page,
				'orderby' => 'id',
				'order' => 'DESC'
			);
			$query = new WP_Query( $args );
			
			$get_posts = $query->get_posts();
			
			$posts = array_merge( $posts, $get_posts );
			
			$total_count = (int) $query->found_posts;
			
			$posts = self::keysToLower( $posts );
			
			if ( $json ) {
				
				header( 'Content-type: application/json' );
				$data = json_encode(
				
					array(
						'total_count' => $total_count,
						'items' => $posts
					)
				);
				
				die( $data );
			}
			
			return $posts;
		}
		
		
		private static function keysToLower( &$obj )
		{
			$type = (int) is_object($obj) - (int) is_array($obj);
			if ($type === 0) return $obj;
			foreach ($obj as $key => &$val)
			{
				$element = self::keysToLower($val);
				switch ($type)
				{
				case 1:
					if (!is_int($key) && $key !== ($keyLowercase = strtolower($key)))
					{
						unset($obj->{$key});
						$key = $keyLowercase;
					}
					$obj->{$key} = $element;
					break;
				case -1:
					if (!is_int($key) && $key !== ($keyLowercase = strtolower($key)))
					{
						unset($obj[$key]);
						$key = $keyLowercase;
					}
					$obj[$key] = $element;
					break;
				}
			}
			return $obj;
		}
		
		
		public static function register_divicomingsoon_settings( $args ) {
			
			register_setting( 
				'divicomingsoon_settings', 
				'dcs_settings', 
				array( 'DiviComingSoon_Admin', 'sanitize' ) 
			);
			
			add_settings_section(
				'dcs_settings_description',
				'Settings',
				array( 'DiviComingSoon_Admin', 'print_description_settings' ),
				'divicomingsoon-settings'
			);
			
			$get_options = array();
			
			$field_name = 'dcs_redirectto';
			$dcs_redirectto = isset( get_option( 'dcs_settings' )[ $field_name ] ) ? esc_attr( get_option( 'dcs_settings' )[ $field_name ] ) : '';
			
			if ( $dcs_redirectto != '' && $dcs_redirectto != NULL ) {
				
				$get_options[0]['title'] = get_the_title( $dcs_redirectto );
				$get_options[0]['value'] = $dcs_redirectto;
			}
			
			$options = array( 
				'type' => 'select',
				'name' => $field_name,
				'placeholder' => 'Choose posts or pages...',
				'options' => $get_options
			);
			
			add_settings_field(
				'dcs_redirectto', 
				'Choose "Coming Soon" page', 
				array( 'DiviComingSoon_Admin', 'parse_fields_callback' ), 
				'divicomingsoon-settings', 
				'dcs_settings_description',
				$options
			);
		}
		
		
		public static function print_description_settings() {
			
			print '';
		}
		
		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public static function sanitize( $input ) {
			
			$new_input = array();
			
			if ( isset( $input['dcs_redirectto'] ) ) {
				
				$new_input['dcs_redirectto'] = sanitize_text_field( $input['dcs_redirectto'] );
			}
			
			return $new_input;
		}
		
		public static function parse_fields_callback( $options ) {
			
			$field_type = isset( $options['type'] ) ? $options['type'] : '';
			
			$field_name = $optionname = isset( $options['name'] ) ? $options['name'] : '';
			
			$field_default_value = isset( $options['default_value'] ) ? $options['default_value'] : '';
			
			$field_placeholder = isset( $options['placeholder'] ) ? $options['placeholder'] : '';
			
			if ( 'select' == $field_type ) {
				
				$valid_options = array();
				
				$selected = isset( self::$options[ $field_name ] ) ? esc_attr( self::$options[ $field_name ] ) : $field_default_value;
				
				if ( $selected != $field_default_value ) {
					
					$field_default_value = $selected;
				}
				
				?>
				<select name="dcs_settings[<?php print $field_name; ?>]" data-defaultvalue="<?php print $field_default_value ?>" data-placeholder="<?php print $field_placeholder ?>" class="select-<?php print $options['name'] ?>">
				<?php
				
				if ( isset( $options['options'] ) ) {
				
					foreach ( $options['options'] as $option ) {
						
						?>
						<option <?php selected( $selected, $option['value'] ); ?> value="<?php print $option['value']; ?>"><?php print $option['title']; ?></option>
						<?php
					}
				}
				
				?>
				</select>
				<?php
			}
		}
		
		
		function add_admin_submenu() {
			
			add_submenu_page( 'et_divi_options', esc_html__( 'Divi Coming Soon', 'DiviComingSoon' ), esc_html__( 'Divi Coming Soon', 'DiviComingSoon' ), 'manage_options', 'divicomingsoon', array( 'DiviComingSoon_Admin', 'admin_settings' ) );
		}
		
		
		public static function admin_settings() {
			
			self::display_configuration_page();
		}
		
		
		public static function display_configuration_page() {
			
			DiviComingSoon_Admin::$options = get_option( 'dcs_settings' );
        ?>
        <div class="wrap">
            <h1>Divi Coming Soon</h1>
            <form id="divicomingsoon_settings" method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'divicomingsoon_settings' );
                do_settings_sections( 'divicomingsoon-settings' );
                submit_button();
            ?>
            </form>
        </div>
	<?php
		
		}
	}
	