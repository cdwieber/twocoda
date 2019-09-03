<?php

	class DiviComingSoon {
		
		private static $initiated = false;
		
		/**
		 * Holds an instance of DiviComingSoon Helper class
		 *
		 * @since 1.0
		 * @var DiviComingSoon_Helper
		 */
		public static $helper;
		
		public static function init() {
			
			if ( ! self::$initiated ) {
				
				self::init_hooks();
			}
		}
		
		/**
		 * Initializes WordPress hooks
		 */
		protected static function init_hooks() {
			
			self::$initiated = true;
			
			// Redirect call
			add_action( 'template_redirect', array( 'DiviComingSoon', 'goToComingSoonPage'), 9);
		}
		
		
		public static function goToComingSoonPage() {
			
			try {
				
				global $post, $wp;
				
				$field_name = 'dcs_redirectto';
				$page_id = isset( get_option( 'dcs_settings' )[ $field_name ] ) ? esc_attr( get_option( 'dcs_settings' )[ $field_name ] ) : '';
				
				if ( $page_id != '' && $page_id != NULL ) {
					
					$path_redirect_to = get_permalink( $page_id );
				
					// Check if user is logged in
					if ( is_user_logged_in() ) {
						return false;
					}
					
					
					// Check for custom login page
					$admin_url = get_admin_url( null, '/' );
					$site_url  = site_url();
					$admin_url = str_replace( $site_url, '', $admin_url );
					$admin_url = str_replace( '/', '', $admin_url );
					
					if ( preg_match("/login|admin|$admin_url/i", $_SERVER['REQUEST_URI'] ) > 0 ) {
						
						return false;
					}
					
					
					// Sets the headers to prevent caching for the different browsers and other popular plugins
					nocache_headers();
					
					if ( !defined('DONOTCACHEPAGE') ) {
						define( 'DONOTCACHEPAGE', true );
					}
					
					if ( !defined( 'DONOTCDN' ) ) {
						define( 'DONOTCDN', true );
					}
					
					if ( !defined( 'DONOTCACHEOBJECT' ) ) {
						define( 'DONOTCACHEOBJECT', true );
					}
					
					if ( !defined( 'DONOTCACHEDB' ) ) {
						define( 'DONOTCACHEDB', true );
					}
					
					
					// Check current url to prevent redirect loop
					$current_url = trailingslashit( home_url( $wp->request ) );
					
					if ( $current_url != $path_redirect_to ) {
					
						wp_redirect( $path_redirect_to );
						exit;
					}
					else {
						
						return false;
					}
				}
			
			} catch (Exception $e) {
			
				self::log( $e );
			}
		}
		
		
		/**
		 * Log debugging infoormation to the error log.
		 *
		 * @param string $e The Exception object
		 */
		protected static function log( $e = FALSE ) {
			
			$data_log = $e;
			
			if ( is_object( $e ) ) {
				
				$data_log = sprintf( "Exception: \n %s \n", $e->getMessage() . "\r\n\r\n" . $e->getFile() . "\r\n" . 'Line:' . $e->getLine() );
			}
			
			if ( apply_filters( 'divicomingsoon_log', defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ) {
				
				error_log( print_r( compact( 'data_log' ), true ) );
			}
		}
	}
	