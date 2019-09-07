<?php
/**
 * TwoCoda Core Schedule. All display functionality related to schedule.
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

		wp_register_script('moment', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar-3.9.0/fullcalendar.min.js');
		wp_register_script('fullcalendar', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar-3.9.0/fullcalendar.min.js', ['jquery', 'moment']);
		wp_register_script('swal', 'https://cdn.jsdelivr.net/npm/sweetalert2@8', null, null, false);
		wp_register_script('schedule-script', site_url() .'/wp-content/plugins/twocoda/assets/js/schedule.js', ['jquery', 'swal']);
		
		
		wp_enqueue_script('swal');
		wp_enqueue_script('moment');
		wp_enqueue_script('fullcalendar');
		wp_enqueue_script('schedule-script');

		wp_localize_script( 'schedule-script', 'tcajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		wp_register_style('fullcalendar-style', site_url() .'/wp-content/plugins/twocoda/assets/js/fullcalendar-3.9.0/fullcalendar.min.css');
		wp_enqueue_style('fullcalendar-style');

		

	}
}
