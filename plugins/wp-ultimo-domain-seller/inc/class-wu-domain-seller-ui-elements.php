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

if (!class_exists('WU_Domain_Seller_UI_Elements')) :

class WU_Domain_Seller_UI_Elements {

  /**
   * Makes sure we are only using one instance of the plugin
   * 
   * @since 0.0.1
   * @var WU_Domain_Seller_UI_Elements
   */
  public static $instance;

  /**
   * Returns the instance of WU_Domain_Seller_UI_Elements
   * 
   * @since 0.0.1
   * @return WU_Domain_Seller_UI_Elements A WU_Domain_Seller_UI_Elements instance
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
     * Register scripts and styles
     */
    add_action('wu_signup_enqueue_scripts', array($this, 'register_scripts'));
    
    /**
     * Localize the scripts with the variables we need
     */
    add_action('wu_signup_enqueue_scripts', array($this, 'add_and_localize_scripts'));

    /**
     * Inject the extra steps fields
     */
    add_action('init', array($this, 'add_extra_signup_steps_and_fields'));

  } // end construct;

  /**
   * Checks if this is the custom domain step
   *
   * @since 0.0.1
   * @return boolean
   */
  public function is_custom_domain_step() {

    return isset($_GET['step']) && $_GET['step'] == 'custom-domain';

  } // end is_custom_domain_step;

  /**
   * Register the scripts and styles needed
   *
   * @since 0.0.1
   * @return void
   */
  public function register_scripts() {

    $suffix = WP_Ultimo()->min;

    wp_register_script('wu-domain-selling-search', WU_Ultimo_Domain_Seller()->get_asset("wu-domain-seller-search$suffix.js", 'js'), array('jquery'));

    wp_register_style('wu-domain-selling-search', WU_Ultimo_Domain_Seller()->get_asset("wu-domain-seller-search$suffix.css", 'css'));

  } // end register_scripts;

  /**
   * Localize the scripts for domain selling
   *
   * @since 0.0.1
   * @return void
   */
  public function add_and_localize_scripts() {

    if (!$this->is_custom_domain_step()) return;

    wp_enqueue_style('wu-domain-selling-search');

    $transient = WU_Signup()->get_transient(false);

    wp_localize_script('wu-domain-selling-search', 'wu_dm_search', array(
      'search'          => isset($transient['blogname']) ? $transient['blogname'] : 'example',
      'selected_domain' => isset($transient['selected-domain']) ? $transient['selected-domain'] : '',
      'ajaxurl'         => admin_url('admin-ajax.php'),
      'wpnonce'         => wp_create_nonce('wu_domain_selling_lookup_domain_nonce'),
    ));

    wp_enqueue_script('wu-domain-selling-search');

  } // end add_and_localize_scripts;

  /**
   * Adds the fields for plans without domain registration support
   *
   * @since 0.0.1
   * @return void
   */
  public function add_fields_for_plans_without_custom_domain() {

    $transient = WU_Signup()->get_transient(false);

    $blogname = isset($transient['blogname']) ? $transient['blogname'] : 'example';

    $base_site_url = WU_Signup()->get_site_url_for_previewer();

    $url = is_subdomain_install() ? sprintf('%s.%s', $blogname, $base_site_url) : sprintf('%s/%s', $base_site_url, $blogname);

    $change_plan_url = add_query_arg('step', 'plan');

    wu_add_signup_field('custom-domain', 'custom_domain_notice', 10, array(
      'name'          => '',
      'type'          => 'html',
      'content'       => "<p>". __("The plan you selected does not support custom domain registration.", 'wu-domain-seller') ."</p><br>",
    ));

    wu_add_signup_field('custom-domain', 'custom_domain_notice_2', 15, array(
      'name'          => '',
      'type'          => 'html',
      'content'       => "<p>". sprintf(__("A custom domain will allow your visitors to access your site via a URL that looks something like %s instead of %s.", 'wu-domain-seller'), "<strong>$blogname.com</strong>", "<strong>$url</strong>") ."</p><br>",
    ));

    wu_add_signup_field('custom-domain', 'custom_domain_button', 20, array(
      'name'          => '',
      'type'          => 'html',
      'content'       => sprintf("<p><a class='button button-streched' href='%s'>%s</a></p><br>", $change_plan_url, __('&larr; Select a Different Plan', 'wu-domain-seller')),
    ));

    wu_add_signup_field('custom-domain', 'submit', 100, array(
      'order'         => 100,
      'type'          => 'submit',
      'name'          => __('Move on to the Next Step &rarr;', 'wu-domain-seller'),
    ));

  } // end add_fields_for_plans_without_custom_domain;

  /**
   * Adds the fields for plans with domain registration support
   *
   * @since 0.0.1
   * @return void
   */
  public function add_fields_for_plans_with_custom_domain() {

    wu_add_signup_field('custom-domain', 'domain-search', 10, array(
      'name'          => apply_filters('wu_signup_custom_domain_label', __('Search Custom Domain', 'wu-domain-seller')),
      'type'          => 'text',
      'default'       => '',
      'placeholder'   => '',
      'tooltip'       => '',
      'required'      => true,
      'attributes'    => array(
        'v-model.trim' => 'search'
      ),
    ));

    $helper_text = WU_Settings::get_setting('domain_selling_helper_text', false);

    if ($helper_text) {

      wu_add_signup_field('custom-domain', 'domain-search-desc', 13, array(
        'name'          => '',
        'type'          => 'html',
        'content'       => sprintf('<p>%s</p><br>', $helper_text),
      ));

    } // end if;

    wu_add_signup_field('custom-domain', 'selected-domain', 15, array(
      'name'          => '',
      'type'          => 'text',
      'default'       => '',
      'placeholder'   => '',
      'tooltip'       => '',
      'required'      => false,
      'attributes'    => array(
        'v-model' => 'selected_domain',
        'v-cloak' => 'v-cloak',
      ),
    ));
    
    wu_add_signup_field('custom-domain', 'domain-search-results', 20, array(
      'name'          => '',
      'type'          => 'html',
      'content'       => $this->get_domain_search_results_template(),
    ));
    
    wu_add_signup_field('custom-domain', 'submit', 100, array(
      'type'          => 'submit',
      'name'          => __('Select Domain and Continue', 'wu-domain-seller'),
      'attributes'    => array(
        'v-bind:disabled' => '!selected_domain',
      ),
    ));

    /**
     * Adds the skip link
     */
    add_filter('wu_signup_form_nav_links', array($this, 'add_skip_links'));

  } // end add_fields_for_plans_with_custom_domain;

  /**
   * Add the Skip Link to the domain custom step
   *
   * @since 0.0.2
   * @param array $links
   * @return array
   */
  public function add_skip_links($links) {

    if (function_exists('WU_Signup') && isset($_REQUEST['step']) && $_REQUEST['step'] == 'custom-domain') {

      $skip_label = apply_filters('wu_domain_seller_skip_label', __('Skip this Step &rarr;', 'wu-domain-seller'));

      return array(
        WU_Signup()->get_next_step_link() => $skip_label
      );

    } // end if;

    return $links;

  } // end add_skip_links;

  /**
   * Adds the new step and the extra fields
   *
   * @since 0.0.1
   * @return void
   */
  public function add_extra_signup_steps_and_fields() {

    /**
     * Check for feature before moving on
     */
    if (!WU_Ultimo_Domain_Seller()->has_domain_selling_feature()) return;

    /**
     * Check if we should add the steps and fields at all
     */
    if (!WU_Settings::get_setting('keep_domain_selling_step', true) && !$this->plan_supports_domain_registration()) return;

    $helper_text = WU_Settings::get_setting('domain_selling_pre_text', false);

    if ($helper_text) {

      /**
       * Tells the customer that they will be able to select 
       * a custom domain in the later steps, if their plan allows it
       * 
       * @since 0.0.1
       */
      wu_add_signup_field('domain', 'custom_domain_notice', 40, array(
        'name'          => __('Site URL Preview', 'wu-domain-seller'),
        'type'          => 'html',
        'content'       => sprintf("<p><small>%s</small></p><br>", $helper_text),
      ));
      
    } // end if;

    /**
     * Adds the Domain Selection Step
     * 
     * @since 0.0.1
     */
    wu_add_signup_step('custom-domain', 35, array(
      'name' => __('Custom Domain', 'wu-domain-seller'),
    ));

    /**
     * Depending on the plan, add the appropriate fields
     */
    if ($this->plan_supports_domain_registration()) {

      /**
       * Load the fields for when the plan selected supports domain registration
       */
      $this->add_fields_for_plans_with_custom_domain();

    } else {

      /**
       * Load the fields for when the plan selected DOESN'T support domain registration
       */
      $this->add_fields_for_plans_without_custom_domain();

    } // end if;

  } // end add_extra_signup_steps_and_fields;

  /**
   * Checks if a given plan supports domain registration
   *
   * @since 0.0.1
   * @return boolean
   */
  public function plan_supports_domain_registration() {

    $transient = WU_Signup()->get_transient(false);

    if (isset($transient['plan_id'])) {

      $plan = wu_get_plan($transient['plan_id']);

      return $plan && $plan->domain_registration;

    } // end if;

    return false;

  } // end plan_supports_domain_registration;

  /**
   * Get the domain search results template
   *
   * @since 0.0.1
   * @return string HTML code for that block
   */
  public function get_domain_search_results_template() {

    ob_start();

      wp_enqueue_script('wp-ultimo');

      WU_Ultimo_Domain_Seller()->render('domain-search-results');

    return ob_get_clean();

  } // end get_domain_search_results_template;

} // end class WU_Domain_Seller_UI_Elements;

WU_Domain_Seller_UI_Elements::get_instance();

endif;