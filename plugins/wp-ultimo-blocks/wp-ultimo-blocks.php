<?php
/**
 * Plugin Name: WP Ultimo: Blocks
 * Description: Use WP Ultimo blocks on the new WordPress block editor!
 * Plugin URI: http://wpultimo.com/addons
 * Text Domain: wu-addon
 * Version: 0.0.1
 * Author: Arindo Duque - NextPress
 * Author URI: http://nextpress.co/
 * Copyright: Arindo Duque, NextPress
 * Network: true
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

if (!class_exists('WP_Ultimo_Blocks')) :

/**
 * Here starts our plugin.
 */
class WP_Ultimo_Blocks {
  
  /**
   * Version of the Plugin
   * 
   * @var string
   */
  public $version = '0.0.1';
  
  /**
   * Makes sure we are only using one instance of the plugin
   * @var object WP_Ultimo_Blocks
   */
  public static $instance;

  /**
   * Returns the instance of WP_Ultimo_Blocks
   * @return object A WP_Ultimo_Blocks instance
   */
  public static function get_instance() {

    if (null === self::$instance) self::$instance = new self();

    return self::$instance;
    
  } // end get_instance;

  /**
   * Initializes the plugin
   */
  public function __construct() {

    // Set the plugins_path
    $this->plugins_path = plugin_dir_path(__DIR__);

    // Load the text domain
    load_plugin_textdomain('wp-ultimo-blocks', false, dirname(plugin_basename(__FILE__)) . '/lang');

    // Updater
    require_once $this->path('inc/class-wu-addon-updater.php');

    /**
     * @since 0.0.1 Creates the updater
     * @var WU_Addon_Updater
     */
    $updater = new WU_Addon_Updater('wp-ultimo-blocks', __('WP Ultimo: Blocks', 'wp-ultimo-blocks'), __FILE__);

    /**
     * Require Files
     */

    // Run Forest, run!
    $this->hooks();

  } // end construct;

  /**
   * Return url to some plugin subdirectory
   * @return string Url to passed path
   */
  public function path($dir) {

    return plugin_dir_path(__FILE__).'/'.$dir;

  } // end path;

  /**
   * Return url to some plugin subdirectory
   * @return string Url to passed path
   */
  public function url($dir) {

    return plugin_dir_url(__FILE__).'/'.$dir;

  } // end url;
  
  /**
   * Return full URL relative to some file in assets
   * @return string Full URL to path
   */
  public function get_asset($asset, $assets_dir = 'img') {

    return $this->url("assets/$assets_dir/$asset");

  } // end get_asset;

  /**
   * Render Views
   * @param string $view View to be rendered.
   * @param Array $vars Variables to be made available on the view escope, via extract().
   */
  public function render($view, $vars = false) {

    // Make passed variables available
    if (is_array($vars)) extract($vars);

    // Load our view
    include $this->path("views/$view.php");

  } // end render;

  /** 
   * Add the hooks we need to make this work
   */
  public function hooks() {

    /**
     * Only run when necessary
     */
    if (!is_main_site() || !$this->has_gutenberg_available()) return;

    /**
     * Adds WP Ultimo Block Category
     */
    add_filter('block_categories', array($this, 'add_wp_ultimo_block_category'), 1, 2);

    /**
     * Load block dependencies
     * 
     * @since 0.0.1
     */
    add_action('enqueue_block_assets', array($this, 'load_dependencies'), 1);

    /**
     * Registers and enqueues the necessary scripts
     * 
     * @since 0.0.1
     */
    add_action('enqueue_block_assets', array($this, 'enqueue_block_assets'));

    /**
     * Get the base block class
     * 
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-block.php');

    /**
     * Block: Pricing Table
     * 
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-block-pricing-table.php');

    /**
     * Block: Template List
     * 
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-block-template-list.php');

    /**
     * Block: Restrict Content
     * 
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-block-restrict-content.php');

  } // end hooks;

  /**
   * Adds the WP Ultimo block category to Gutenberg
   *
   * @since 0.0.1
   * @param array $categories
   * @param array $post
   * @return array
   */
  public function add_wp_ultimo_block_category($categories, $post) {
    
    return array_merge($categories, array(
      array(
        'slug' => 'wp-ultimo',
        'title' => __( 'WP Ultimo', 'mario-blocks' ),
      ),
    ));

  } // end add_wp_ultimo_block_category;

  /**
   * Check if Gutenberg is available
   *
   * @since 1.9.6
   * @return boolean
   */
  public function has_gutenberg_available() {

    return function_exists('register_block_type');

  } // end has_gutenberg_available;

  /**
   * Get template options
   *
   * @since 0.0.1
   * @return array
   */
  public function get_template_options() {

    $templates = is_admin() ? WU_Site_Templates::prepare_site_templates()[1] : array();

    return array_map(function($template) {
      return array(
        'key'   => 'wu_template_' . $template['id'],
        'label' => $template['name'] . ' (#' . $template['id'] . ')',
        'value' => $template['id'],
      );
    }, $templates);

  } // end get_template_options;

  /**
   * Get the plan otions
   *
   * @since 0.0.1
   * @return array
   */
  public function get_plan_options() {

    $plans = is_admin() ? WU_Plans::get_plans(true) : array();

    return array_map(function($plan) {
      return array(
        'key'   => 'wu_plan_' . $plan->id,
        'label' => $plan->title,
        'value' => $plan->id,
      );
    }, $plans);

  } // end get_plan_options;

  /**
   * Enqueue block assets
   *
   * @since 0.0.1
   * @return void
   */
  public function load_dependencies() {

    // Scripts.
		wp_register_script(
			'wp-ultimo-blocks',                                                                   // Handle.
			WP_Ultimo_Blocks()->url('blocks/dist/blocks.build.js'),                               // Block.build.js: We register the block here. Built with Webpack.
			array('wu-pricing-table', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),   // Dependencies, defined above.
			WP_Ultimo_Blocks()->version,
			true
    );

    wp_localize_script('wp-ultimo-blocks', 'wu_blocks', array(
      'plans'     => $this->get_plan_options(),
      'templates' => $this->get_template_options(),
    ));
    
    // Styles.
		wp_enqueue_style(
			'wp-ultimo-block-pricing-table-css',                              // Handle.
			WP_Ultimo_Blocks()->url('blocks/dist/blocks.editor.build.css'),   // Block editor CSS.
			array('wp-edit-blocks'),                                          // Dependency to include the CSS after it.
			WP_Ultimo_Blocks()->version
		);

  } // end load_dependencies;

  /**
   * Enqueue block assets
   *
   * @since 0.0.1
   * @return void
   */
  public function enqueue_block_assets() {

		$suffix = WP_Ultimo()->min;

		// Styles.
		wp_enqueue_style(
			'wp-ultimo-block',                                               // Handle.
			WP_Ultimo_Blocks()->url('blocks/dist/blocks.style.build.css'),   // Block style CSS.
			array( 'wp-editor', 'wu-pricing-table', 'wu-shortcodes' ),       // Dependency to include the CSS after it.
			WP_Ultimo_Blocks()->version
		);

  } // end enqueue_block_assets;

} // end WP_Ultimo_Blocks;

/**
 * Returns the active instance of the plugin
 *
 * @return void
 */
function WP_Ultimo_Blocks() {

  return WP_Ultimo_Blocks::get_instance();

} // end WP_Ultimo_Blocks;

/**
 * Initialize the Plugin
 */
add_action('plugins_loaded', 'wp_ultimo_blocks_init', 20);

/**
 * We require WP Ultimo, so we need it
 *
 * @since 0.0.1
 * @return void
 */
function wp_ultimo_blocks_requires_ultimo() { ?>

  <div class="notice notice-warning"> 
    <p><?php _e('WP Ultimo: Blocks requires WP Ultimo to run. Install and active WP Ultimo to use WP Ultimo: Blocks.', 'wp-ultimo-blocks'); ?></p>
  </div>

<?php } // end wp_ultimo_blocks_requires_ultimo

/**
 * Initializes the plugin
 *
 * @since 0.0.1
 * @return void
 */
function wp_ultimo_blocks_init() {

  if (!class_exists('WP_Ultimo')) {

    return add_action('network_admin_notices', 'wp_ultimo_blocks_requires_ultimo');

  } // We require WP Ultimo, baby

  if (!function_exists('register_block_type')) {

    return WP_Ultimo()->add_message(__('WP Ultimo: Blocks requires WordPress >= 5.0 or Gutenberg to be active.', 'wp-ultimo-blocks'), 'warning', true);

  } // end if;

  if (!version_compare(WP_Ultimo()->version, '1.9.7', '>=')) {

    return WP_Ultimo()->add_message(__('WP Ultimo: Blocks requires WP Ultimo version 1.9.7.', 'wp-ultimo-blocks'), 'warning', true);

  } // end if;

  // Set global
  $GLOBALS['WP_Ultimo_Blocks'] = WP_Ultimo_Blocks();

} // end wp_ultimo_blocks_init;

endif;