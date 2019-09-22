<?php
/**
 * EM Booking Admin Schedule.
 *
 * Functionality related to the display and interactivity
 * of the admin-view scheduler.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */
class EMB_Admin_Schedule {
	/**
	 * Parent plugin class.
	 *
	 * @var    EM_Booking
	 */
	protected $plugin = null;
	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected static $key = 'em_booking_schedule';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $title = 'Schedule';
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
	 * @param  EM_Booking $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
		// Set our title.
		$this->title = esc_attr__( 'Schedule', 'errigal-booking-module' );
	}
	/**
	 * Initiate our hooks.
	 */
	public function hooks() {
		// Hook in our actions to the admin.
		add_action( 'admin_enqueue_scripts', [ $this, 'schedule_scripts_styles' ], 999 );

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

	}
	/**
	 * Register our setting to WP.
	 */
	public function admin_init() {
		register_setting( self::$key, self::$key );
	}
	/**
	 * Add menu options page.
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			self::$key,
			array( $this, 'admin_page_display' ),
			'dashicons-calendar'
		);
	}
	/**
	 * Admin page markup.
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
		wp_register_script( 'moment', $this->plugin->url . '/assets/js/fullcalendar-3.9.0/lib/moment.min.js' );
		wp_register_script( 'fullcalendar', $this->plugin->url . '/assets/js/fullcalendar-3.9.0/fullcalendar.min.js', [ 'jquery', 'moment' ] );
		wp_register_script( 'swal', 'https://cdn.jsdelivr.net/npm/sweetalert2@8', null, null, false );
		wp_register_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' );
		wp_register_script( 'bootstrap4-script', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', [ 'jquery', 'popper' ] );
		wp_register_script( 'datetimepicker', 'https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js', [ 'jquery' ], null );
		wp_register_script( 'schedule-script', $this->plugin->url . '/assets/js/schedule.js', [ 'jquery', 'swal', 'datetimepicker' ], null, true );

		wp_register_style( 'fullcalendar-style', $this->plugin->url . '/assets/js/fullcalendar-3.9.0/fullcalendar.min.css' );
		wp_register_style( 'bootstrap4-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' );
		wp_register_style( 'datetimepicker-style', 'https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css' );
		wp_register_style('em-booking-main-style', $this->plugin->url . '/assets/css/style.css' );

		// Load these assets specifically on the scheduler page.
		if ( $_GET['page'] == 'em_booking_schedule' ) {
			wp_enqueue_script( 'swal' );
			wp_enqueue_script( 'moment' );
			wp_enqueue_script( 'fullcalendar' );
			wp_enqueue_script( 'schedule-script' );
			wp_enqueue_script( 'popper' );
			wp_enqueue_script( 'bootstrap4-script' );
			wp_enqueue_script( 'datetimepicker' );

			wp_localize_script( 'schedule-script', 'tcajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

			wp_enqueue_style( 'fullcalendar-style' );
			wp_enqueue_style( 'bootstrap4-style' );
			wp_enqueue_style( 'datetimepicker-style' );
			wp_enqueue_style('em-booking-main-style');

			// Swap WP's super-outdated jquery with the one we need from the CDN.
			// TODO: Upgrade the rest of the modules to stop them complaining in console.
			wp_deregister_script( 'jquery' );
			wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, true );
		}

	}
}
