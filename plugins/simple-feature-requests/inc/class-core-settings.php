<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'JCK_SFR_Core_Settings' ) ) {
	return;
}

/**
 * JCK_SFR_Core_Settings.
 *
 * @class    JCK_SFR_Core_Settings
 * @version  1.0.5
 * @category Class
 * @author   Iconic
 */
class JCK_SFR_Core_Settings {
	/**
	 * Single instance of the JCK_SFR_Core_Settings object.
	 *
	 * @var JCK_SFR_Core_Settings
	 */
	public static $single_instance = null;

	/**
	 * Class args.
	 *
	 * @var array
	 */
	public static $args = array();

	/**
	 * Settings framework instance.
	 *
	 * @var JCK_SFR_Settings_Framework
	 */
	public static $settings_framework = null;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Docs base url.
	 *
	 * @var string
	 */
	public static $docs_base = 'https://docs.iconicwp.com';

	/**
	 * Iconic svg src.
	 *
	 * @var string
	 */
	public static $iconic_svg = 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjMwcHgiIGhlaWdodD0iMzUuNDU1cHgiIHZpZXdCb3g9IjAgMCAzMCAzNS40NTUiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDMwIDM1LjQ1NSIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8Zz4NCgkJPHBvbHlnb24gcG9pbnRzPSIxMC45MSwzMy44MTggMTMuNjM2LDM1LjQ1NSAxMy42MzYsMTkuMDkxIDEwLjkxLDE3LjQ1NSAJCSIvPg0KCQk8cG9seWdvbiBwb2ludHM9IjE2LjM2MywzNS40NTUgMzAsMjcuMTY4IDMwLDIzLjk3NiAxNi4zNjMsMzIuMjYzIAkJIi8+DQoJCTxnPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIxMi4zNSwxLjU5IDI1Ljk4Niw5Ljc3MiAyOC42MzcsOC4xODIgMTUsMCAJCQkiLz4NCgkJCTxwb2x5Z29uIHBvaW50cz0iNS40NTUsMzAuNTQ1IDguMTgyLDMyLjE4MiA4LjE4MiwxNS44MTggNS40NTUsMTQuMTgyIAkJCSIvPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIxNi4zNjMsMjguOTIxIDMwLDIwLjYzNCAzMCwxNy40NDIgMTYuMzYzLDI1LjcyOSAJCQkiLz4NCgkJCTxwb2x5Z29uIHBvaW50cz0iNi44NzEsNC45ODQgMjAuNTA4LDEzLjE2NyAyMy4xNTgsMTEuNTc2IDkuNTIxLDMuMzk1IAkJCSIvPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIyLjcyNywxMi41NDUgMCwxMC45MDkgMCwyNy4yNzMgMi43MjcsMjguOTA5IAkJCSIvPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIxNi4zNjMsMjIuMzg4IDMwLDE0LjEgMzAsMTAuOTA5IDE2LjM2MywxOS4xOTYgCQkJIi8+DQoJCQk8cG9seWdvbiBwb2ludHM9IjEuMzkyLDguMTY1IDE1LjAyOCwxNi4zNDcgMTcuNjc4LDE0Ljc1NiA0LjA0Miw2LjU3NSAJCQkiLz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K';

	/**
	 * Creates/returns the single instance JCK_SFR_Core_Settings object.
	 *
	 * @return JCK_SFR_Core_Settings
	 */
	public static function run( $args = array() ) {
		if ( null === self::$single_instance ) {
			self::$args            = $args;
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 20 );
		add_action( 'admin_notices', array( __CLASS__, 'account_getting_started' ), 1 );
	}

	/**
	 * Init.
	 */
	public static function init() {
		require_once( self::$args['vendor_path'] . 'wp-settings-framework/wp-settings-framework.php' );

		add_filter( 'wpsf_register_settings_' . self::$args['option_group'], array( __CLASS__, 'setup_dashboard' ) );

		self::$settings_framework = new JCK_SFR_Settings_Framework( self::$args['settings_path'], self::$args['option_group'] );
		self::$settings           = self::$settings_framework->get_settings();
	}

	/**
	 * Get setting.
	 *
	 * @param $setting
	 *
	 * @return mixed
	 */
	public static function get_setting( $setting ) {
		if ( empty( self::$settings ) ) {
			return null;
		}

		if ( ! isset( self::$settings[ $setting ] ) ) {
			return null;
		}

		return self::$settings[ $setting ];
	}

	/**
	 * Add settings page.
	 */
	public static function add_settings_page() {
		$default_title = sprintf( '<div style="padding-bottom: 15px;"><img width="24" height="28" style="display: inline-block; vertical-align: text-bottom; margin: 0 8px 0 0" src="%s"> %s by <a href="https://iconicwp.com/?utm_source=simple-feature-requests&utm_medium=insideplugin" target="_blank">Iconic</a> <em style="opacity: 0.6; font-size: 80%%;">(v%s)</em></div>', esc_attr( self::$iconic_svg ), self::$args['title'], self::$args['version'] );

		self::$settings_framework->add_settings_page( array(
			'parent_slug' => isset( self::$args['parent_slug'] ) ? self::$args['parent_slug'] : 'woocommerce',
			'page_title'  => isset( self::$args['page_title'] ) ? self::$args['page_title'] : $default_title,
			'menu_title'  => self::$args['menu_title'],
			'capability'  => self::get_settings_page_capability(),
		) );

		do_action( 'admin_menu_' . self::$args['option_group'] );
	}

	/**
	 * Get settings page capability.
	 *
	 * @return mixed
	 */
	public static function get_settings_page_capability() {
		$capability = isset( self::$args['capability'] ) ? self::$args['capability'] : 'manage_woocommerce';

		return apply_filters( self::$args['option_group'] . '_settings_page_capability', $capability );
	}

	/**
	 * Is settings page?
	 *
	 * @param string $suffix
	 *
	 * @return bool
	 */
	public static function is_settings_page( $suffix = '' ) {
		if ( ! is_admin() ) {
			return false;
		}

		$path = str_replace( '_', '-', self::$args['option_group'] ) . '-settings' . $suffix;

		if ( empty( $_GET['page'] ) || $_GET['page'] !== $path ) {
			return false;
		}

		return true;
	}

	/**
	 * Add getting started notice to settings pages.
	 */
	public static function account_getting_started() {
		if ( ! self::is_settings_page() && ! self::is_settings_page( '-account' ) ) {
			return;
		}

		if ( empty( self::$args['docs']['getting-started'] ) ) {
			return;
		}

		$option_name = self::$args['option_group'] . '_notice_dismiss_getting_started';
		$dismissed   = get_option( $option_name, false );

		if ( $dismissed ) {
			return;
		}

		$dismiss = filter_input( INPUT_POST, $option_name );

		if ( $dismiss ) {
			update_option( $option_name, true );

			return;
		}
		?>
		<style>
			.iconic-notice {
				padding: 35px 30px;
				background-color: #fff;
				margin: 20px 20px 20px 0;
				box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
				font-size: 15px;
				position: relative;
				border-left: 4px solid #2BA499;
			}

			.iconic-notice h2 {
				margin: 0 0 1.2em;
				font-size: 23px;
				position: relative;
				line-height: 1.2em;
			}

			.iconic-notice h3 {
				margin: 0 0 1.5em;
			}

			.iconic-notice p,
			.iconic-notice li {
				font-size: 15px;
			}

			.iconic-notice li {
				margin: 0 0 10px;
			}

			.iconic-notice p,
			.iconic-notice ol,
			.iconic-notice ul {
				margin-bottom: 2em;
			}

			.iconic-notice :last-child {
				margin-bottom: 0;
			}

			.iconic-notice__dismiss {
				position: absolute;
				top: 20px;
				right: 20px;
			}

			.iconic-notice__dismiss button {
				background: none;
				border: none;
				padding: 0;
				margin: 0;
				cursor: pointer;
				color: #2BA499;
				outline: none;
			}

			.iconic-notice__dismiss button:hover,
			.iconic-notice__dismiss button:active {
				color: #248C84;
			}
		</style>
		<div class="iconic-notice iconic-notice--getting-started">
			<form action="" method="post" class="iconic-notice__dismiss">
				<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>" value="1">
				<button title="<?php echo esc_attr( __( 'Dismiss', 'simple-feature-requests' ) ); ?>">
					<span class="dashicons dashicons-dismiss"></span></button>
			</form>
			<h2>Welcome to <?php echo self::$args['title']; ?>!</a></h2>
			<p><?php _e( "Thank you for choosing Iconic. We've put together some useful links to help you get started:", 'simple-feature-requests' ); ?></p>

			<?php self::output_getting_started_links(); ?>

			<p><strong><?php _e( 'Is something not quite right?', 'simple-feature-requests' ); ?></strong> <?php printf( __( 'Take a look at our
				<a href="%s" target="_blank">troubleshooting documentation</a>', 'simple-feature-requests' ), self::get_docs_url( 'troubleshooting' ) ); ?>. <?php _e( 'There is a permanent link to the plugin documentation in the "Support" section below.', 'simple-feature-requests' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Get doc links.
	 *
	 * @return array
	 */
	public static function get_doc_links() {
		$transient_name = self::$args['option_group'] . '_getting_started_links';

		if ( false !== ( $return = get_transient( $transient_name ) ) ) {
			return $return;
		}

		$return = array();
		$url    = self::get_docs_url( 'getting-started' );
		$html   = file_get_contents( $url );

		if ( ! $html ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		$dom = new DOMDocument;

		@$dom->loadHTML( $html );

		$lists = $dom->getElementsByTagName( 'ul' );

		if ( empty( $lists ) ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		foreach ( $lists as $list ) {
			$classes = $list->getAttribute( 'class' );

			if ( strpos( $classes, 'articleList' ) === false ) {
				continue;
			}

			$links = $list->getElementsByTagName( 'a' );

			foreach ( $links as $link ) {
				$return[] = array(
					'href'  => $link->getAttribute( 'href' ),
					'title' => $link->nodeValue,
				);
			}
		}

		set_transient( $transient_name, $return, 30 * DAY_IN_SECONDS );

		return $return;
	}

	/**
	 * Output getting started links.
	 */
	public static function output_getting_started_links() {
		$links = self::get_doc_links();

		if ( empty( $links ) ) {
			return;
		} ?>
		<h3><?php _e( 'Getting Started', 'simple-feature-requests' ); ?></h3>

		<ol>
			<?php foreach ( $links as $link ) { ?>
				<li>
					<a href="<?php echo esc_attr( self::get_docs_url() . $link['href'] ); ?>?utm_source=simple-feature-requests&utm_medium=insideplugin" target="_blank"><?php echo $link['title']; ?></a>
				</li>
			<?php } ?>
		</ol>
	<?php }

	/**
	 * Get docs URL.
	 *
	 * @param bool $type
	 *
	 * @return mixed|string
	 */
	public static function get_docs_url( $type = false ) {
		if ( ! $type || $type === 'base' || ! isset( self::$args['docs'][ $type ] ) ) {
			return self::$docs_base;
		}

		return self::$docs_base . self::$args['docs'][ $type ];
	}

	/**
	 * Configure settings dashboard.
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public static function setup_dashboard( $settings ) {
		if ( ! self::is_settings_page() ) {
			return $settings;
		}

		$settings['tabs']     = isset( $settings['tabs'] ) ? $settings['tabs'] : array();
		$settings['sections'] = isset( $settings['sections'] ) ? $settings['sections'] : array();

		$settings['tabs'][] = array(
			'id'    => 'dashboard',
			'title' => __( 'Dashboard', 'simple-feature-requests' ),
		);

		if ( current_user_can( 'manage_options' ) && ! defined( 'ICONIC_DISABLE_DASH' ) ) {
			$settings['sections']['licence'] = array(
				'tab_id'              => 'dashboard',
				'section_id'          => 'general',
				'section_title'       => __( 'License &amp; Account Settings', 'simple-feature-requests' ),
				'section_description' => '',
				'section_order'       => 10,
				'fields'              => array(
					array(
						'id'       => 'licence',
						'title'    => __( 'License &amp; Billing', 'simple-feature-requests' ),
						'subtitle' => __( 'Activate or sync your license, cancel your subscription, print invoices, and manage your account information.', 'simple-feature-requests' ),
						'type'     => 'custom',
						'default'  => JCK_SFR_Core_Licence::admin_account_link(),
					),
					array(
						'id'       => 'account',
						'title'    => __( 'Your Account', 'simple-feature-requests' ),
						'subtitle' => __( 'Manage all of your Iconic plugins, supscriptions, renewals, and more.', 'simple-feature-requests' ),
						'type'     => 'custom',
						'default'  => self::account_link(),
					),
				),

			);
		}

		$settings['sections']['support'] = array(
			'tab_id'              => 'dashboard',
			'section_id'          => 'support',
			'section_title'       => __( 'Support', 'simple-feature-requests' ),
			'section_description' => '',
			'section_order'       => 30,
			'fields'              => array(
				array(
					'id'       => 'support',
					'title'    => __( 'Support', 'simple-feature-requests' ),
					'subtitle' => __( 'Get premium support with a valid license.', 'simple-feature-requests' ),
					'type'     => 'custom',
					'default'  => self::support_link(),
				),
				array(
					'id'       => 'documentation',
					'title'    => __( 'Documentation', 'simple-feature-requests' ),
					'subtitle' => __( 'Read the plugin documentation.', 'simple-feature-requests' ),
					'type'     => 'custom',
					'default'  => self::documentation_link(),
				),
			),
		);

		if ( current_user_can( 'manage_options' ) && ! defined( 'ICONIC_DISABLE_DASH' ) ) {
			if ( ! empty( self::$args['cross_sells'] ) ) {
				$cross_sells = JCK_SFR_Core_Cross_Sells::run( array(
					'plugins' => self::$args['cross_sells'],
				) );

				$settings['sections']['cross-sells'] = array(
					'tab_id'              => 'dashboard',
					'section_id'          => 'cross-sells',
					'section_title'       => __( 'Works Well With...', 'simple-feature-requests' ),
					'section_description' => $cross_sells::get_output(),
					'section_order'       => 40,
					'fields'              => array(),
				);
			}
		}

		return $settings;
	}

	/**
	 * Get support button.
	 *
	 * @return string
	 */
	public static function support_link() {
		return sprintf( '<a href="%s" class="button button-secondary" target="_blank">%s</a>', 'https://iconicwp.com/support?utm_source=simple-feature-requests&utm_medium=insideplugin', __( 'Submit Ticket', 'simple-feature-requests' ) );
	}

	/**
	 * Get documentation button.
	 *
	 * @return string
	 */
	public static function documentation_link() {
		return sprintf( '<a href="%s" class="button button-secondary" target="_blank">%s</a>', self::get_docs_url( 'collection' ), __( 'Read Documentation', 'simple-feature-requests' ) );
	}

	/**
	 * Get account button.
	 *
	 * @return string
	 */
	public static function account_link() {
		return sprintf( '<a href="%s" class="button button-secondary" target="_blank">%s</a>', 'https://iconicwp.com/account?utm_source=simple-feature-requests&utm_medium=insideplugin', __( 'Manage Your Account', 'simple-feature-requests' ) );
	}
}