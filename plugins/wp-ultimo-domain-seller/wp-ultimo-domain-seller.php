<?php
/**
 * Plugin Name: WP Ultimo: Domain Seller
 * Description: Sell custom domains to your customers right from the registration screen! Supports OpenSRS for now, with more options to come.
 * Plugin URI: http://wpultimo.com/addons
 * Text Domain: wu-domain-seller
 * Version: 0.0.3
 * Author: Arindo Duque - NextPress
 * Author URI: http://nextpress.co/
 * Copyright: Arindo Duque, NextPress
 * Network: true
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * WP Ultimo: Domain Seller on Sign-up is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Ultimo: Domain Seller on Sign-up is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Ultimo: Domain Seller on Sign-up. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author   Arindo Duque
 * @category Core
 * @package  Addons
 * @version  0.0.3
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

if (!class_exists('WU_Ultimo_Domain_Seller')) :

/**
 * Here starts our plugin.
 */
class WU_Ultimo_Domain_Seller {
  
  /**
   * Version of the Plugin
   * 
   * @var string
   */
  public $version = '0.0.3';
  
  /**
   * Makes sure we are only using one instance of the plugin
   * 
   * @since 0.0.1
   * @var object WU_Ultimo_Domain_Seller
   */
  public static $instance;

  /**
   * Returns the instance of WU_Ultimo_Domain_Seller
   * 
   * @since 0.0.1
   * @return object A WU_Ultimo_Domain_Seller instance
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
    load_plugin_textdomain('wu-domain-seller', false, dirname(plugin_basename(__FILE__)) . '/lang');

    // Updater
    require_once $this->path('inc/class-wu-addon-updater-free.php');

    /**
     * @since 0.0.1 Creates the updater
     * @var WU_Addon_Updater_Free
     */
    $updater = new WU_Addon_Updater_Free('wp-ultimo-domain-seller', __('WP Ultimo - Feature Plugin: Domain Selling', 'wu-domain-seller'), __FILE__);

    /**
     * Require Files
     */

    // Run Forest, run!
    $this->hooks();

  } // end construct;

  /**
   * Return url to some plugin subdirectory
   * 
   * @since 0.0.1
   * @return string Url to passed path
   */
  public function path($dir) {

    return plugin_dir_path(__FILE__).'/'.$dir;

  } // end path;

  /**
   * Return url to some plugin subdirectory
   * 
   * @since 0.0.1
   * @return string Url to passed path
   */
  public function url($dir) {

    return plugin_dir_url(__FILE__).'/'.$dir;

  } // end url;
  
  /**
   * Return full URL relative to some file in assets
   * 
   * @since 0.0.1
   * @return string Full URL to path
   */
  public function get_asset($asset, $assets_dir = 'img') {

    return $this->url("assets/$assets_dir/$asset");

  } // end get_asset;

  /**
   * Render Views
   * 
   * @since 0.0.1
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
   * 
   * @since 0.0.1
   * @return void
   */
  public function hooks() {

    /**
     * Base class for providers
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-domain-seller-base.php');
    
    /**
     * Loads the dispatcher. This handles the actions and ajax endpoints
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-domain-seller-action-dispatcher.php');
    
    /**
     * Loads and registers the OpenSRS provider
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-domain-seller-opensrs.php');
    
    /**
     * Developers, 
     * 
     * If you want to implement your own provider
     * take a look on the file below.
     * 
     * Uncommenting this line will add the example provider you can tweek that or use it
     * as an starting point to implement your own providers
     * @since 0.0.1
     */
    // require_once $this->path('inc/class-wu-domain-seller-example.php');

    /**
     * Changes the UI elements to add the domain search capabilities
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-domain-seller-ui-elements.php');

    /**
     * Adds the plan options on the plan edit page and more
     * @since 0.0.1
     */
    require_once $this->path('inc/class-wu-domain-seller-plan-support.php');

  } // end hooks;

  /**
   * Checks if we have the feature enabled before adding all this overhead
   *
   * @since 0.0.1
   * @return boolean
   */
  public function has_domain_selling_feature() {

    return WU_Settings::get_setting('enable_domain_selling', false);

  } // end has_domain_selling_feature;

} // end WU_Ultimo_Domain_Seller;

/**
 * Returns the active instance of the plugin
 *
 * @since 0.0.1
 * @return void
 */
function WU_Ultimo_Domain_Seller() {

  return WU_Ultimo_Domain_Seller::get_instance();

} // end WU_Ultimo_Domain_Seller;

/**
 * Initialize the Plugin
 */
add_action('plugins_loaded', 'wu_domain_seller_init', 1);

/**
 * We require WP Ultimo, so we need it
 *
 * @since 0.0.1
 * @return void
 */
function wu_domain_seller_requires_ultimo() { ?>

  <div class="notice notice-warning"> 
    <p><?php _e('WP Ultimo - Feature Plugin: Domain Selling requires WP Ultimo to run. Install and active WP Ultimo to use WP Ultimo - Feature Plugin: Domain Selling.', 'wu-domain-seller'); ?></p>
  </div>

<?php } // end wu_domain_seller_requires_ultimo

/**
 * Initializes the plugin
 *
 * @since 0.0.1
 * @return void
 */
function wu_domain_seller_init() {

  if (!class_exists('WP_Ultimo')) {

    return add_action('network_admin_notices', 'wu_domain_seller_requires_ultimo');

  } // We require WP Ultimo, baby

  if (!version_compare(WP_Ultimo()->version, '1.6.0', '>=')) {

    return WP_Ultimo()->add_message(__('WP Ultimo - Feature Plugin: Domain Selling requires WP Ultimo version 1.6.0. ', 'wu-domain-seller'), 'warning', true);

  } // end if;

  // Set global
  $GLOBALS['WU_Ultimo_Domain_Seller'] = WU_Ultimo_Domain_Seller();

} // end wu_domain_seller_init;

endif;