<?php
/**
 * Domain Seller Dispatcher
 *
 * Adds the ajax endpoints and routes requests depending on the desired action.
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Domain_Seller
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WU_Domain_Seller_Plan_Support')) :

class WU_Domain_Seller_Plan_Support {

  /**
   * Makes sure we are only using one instance of the plugin
   * 
   * @since 0.0.1
   * @var WU_Domain_Seller_Plan_Support
   */
  public static $instance;

  /**
   * Returns the instance of WU_Domain_Seller_Plan_Support
   * 
   * @since 0.0.1
   * @return WU_Domain_Seller_Plan_Support A WU_Domain_Seller_Plan_Support instance
   */
  public static function get_instance() {

    if (null === self::$instance) self::$instance = new self();

    return self::$instance;
    
  } // end get_instance;

  /**
   * Initializes the class
   *
   * @since 0.0.1
   * @return void
   */
  public function __construct() {

    /**
     * Adds the Domain Registration tab on the plan edit page
     */
    add_filter('wu_plans_advanced_options_tabs', array($this, 'add_plan_tab'));

    /**
     * Adds the Options for the Tab
     */
    add_action('wu_plans_advanced_options_after_panels', array($this, 'add_plan_tab_content'));

    /**
     * Save the domain registration options to the plan
     */
    add_action('wu_save_plan', array($this, 'save_plan_domain_selling_options'));

  } // end construct;

  /**
   * Adds the Domain Registration tab on the plan edit page
   *
   * @since 0.0.1
   * @param array $tabs
   * @return array
   */
  public function add_plan_tab($tabs) {

    /**
     * Check for feature before moving on
     */
    if (!WU_Ultimo_Domain_Seller()->has_domain_selling_feature()) return $tabs;

    $tabs['domain_registration'] = __('Domain Register', 'wu-domain-seller');

    return $tabs;

  } // end add_plan_tab;

  /**
   * Renders the domain registration tab options
   *
   * @since 0.0.1
   * @param WU_Plan $plan
   * @return void
   */
  public function add_plan_tab_content($plan) {

    /**
     * Check for feature before moving on
     */
    if (!WU_Ultimo_Domain_Seller()->has_domain_selling_feature()) return;

    WU_Ultimo_Domain_Seller()->render('options-panel', array(
      'plan' => $plan
    ));

  } // end add_plan_tab_content;

  /**
   * Saves the domain registration options to the plan
   * 
   * @since 0.0.1
   * @param  WU_Plan $plan
   * @return void
   */
  public function save_plan_domain_selling_options($plan) {

    /**
     * Check for feature before moving on
     */
    if (!WU_Ultimo_Domain_Seller()->has_domain_selling_feature()) return;

    if (is_a($plan, 'WU_Plan') && isset($_POST['domain_registration'])) {

      update_post_meta($plan->id, 'wpu_domain_registration', 1);

    } else {

      update_post_meta($plan->id, 'wpu_domain_registration', 0);

    } // end if;

  } // end save_plan_domain_selling_options;

} // end class WU_Domain_Seller_Plan_Support;

WU_Domain_Seller_Plan_Support::get_instance();

endif;