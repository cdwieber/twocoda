<?php
/**
 * TwoCoda Core Schedule.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */

//require_once dirname( __FILE__ ) . '/../vendor/cmb2/init.php';

/**
 * TwoCoda Core Schedule class.
 *
 * @since 0.0.1
 */
class TC_Schedule {
	/**
	 * Parent plugin class.
	 *
	 * @var    TwoCoda_Core
	 * @since  0.0.1
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected static $key = 'twocoda_schedule';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected static $metabox_id = 'twocoda_schedule_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
	 *
	 * @param  TwoCoda_Core $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Set our title.
		$this->title = esc_attr__( 'Schedule', 'twocoda' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {

		// Hook in our actions to the admin.

		add_action( 'admin_enqueue_scripts', [$this, 'schedule_scripts_styles'] );
		
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		
		
	}

	/**
	 * Register our setting to WP.
	 *
	 * @since  0.0.1
	 */
	public function admin_init() {
		register_setting( self::$key, self::$key );
	}

	/**
	 * Add menu options page.
	 *
	 * @since  0.0.1
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			self::$key,
			array( $this, 'admin_page_display' ),
			'dashicons-calendar',
		);
	}

	/**
	 * Admin page markup.
	 *
	 * @since  0.0.1
	 */
	public function admin_page_display() {
		?>
		<div class="wrap options-page <?php echo esc_attr( self::$key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php 
			 require_once 'layouts/schedule.inc.php';
			?>
		</div>
		<?php
	}

	public function schedule_scripts_styles() {
		//TODO: Get these to load on the specific view

		wp_register_script('fullcalendar-core', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/core/main.min.js', 'jquery', null, true);
		wp_register_script('fullcalendar-daygrid', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/daygrid/main.min.js', 'fullcalendar-core', '', true);
		wp_register_script('fullcalendar-timegrid', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/timegrid/main.min.js', 'fullcalendar-core', '', true);
		wp_register_script('fullcalendar-list', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/list/main.min.js', 'fullcalendar-core', '', '',);
		wp_register_script('fullcalendar-interaction', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/interaction/main.min.js', 'fullcalendar-core','',true);
		wp_register_script('schedule-script', site_url() .'/wp-content/plugins/twocoda/assets/js/schedule.js', 
		['fullcalendar-core', 
		'fullcalendar-daygrid', 
		'fullcalendar-timegrid', 
		'fullcalendar-list',
		'fullcalendar-interaction'], '');
		//These dependencies are VERY DELICATE. Do not taunt.


		wp_enqueue_script('fullcalendar-core');
		wp_enqueue_script('fullcalendar-daygrid');
		wp_enqueue_script('fullcalendar-timegrid');
		wp_enqueue_script('fullcalendar-list');
		wp_enqueue_script('fullcalendar-interaction');
		wp_enqueue_script('schedule-script');

		wp_register_style('fullcalendar-core-style', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/core/main.min.css');
		wp_register_style('fullcalendar-daygrid-style', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/daygrid/main.min.css', 'fullcalendar-core-style');
		wp_register_style('fullcalendar-timegrid-style', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/timegrid/main.min.css', 'fullcalendar-core-style');
		wp_register_style('fullcalendar-list-style', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar/packages/list/main.min.css', 'fullcalendar-core-style');

		wp_enqueue_style('fullcalendar-core-style');
		wp_enqueue_style('fullcalendar-daygrid-style');
		wp_enqueue_style('fullcalendar-timegrid-style');
		wp_enqueue_style('fullcalendar-list-style');


		//Fucking christ, am I really gonna add another script?
		wp_register_script('swal', 'https://cdn.jsdelivr.net/npm/sweetalert2@8', null, null, false);
		wp_enqueue_script('swal');

	}
}
