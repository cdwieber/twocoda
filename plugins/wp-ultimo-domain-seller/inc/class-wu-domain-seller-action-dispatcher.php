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

if (!class_exists('WU_Domain_Seller_Action_Dispatcher')) :

class WU_Domain_Seller_Action_Dispatcher {

  /**
   * Makes sure we are only using one instance of the plugin
   * 
   * @since 0.0.1
   * @var WU_Domain_Seller_Action_Dispatcher WU_Domain_Seller_Action_Dispatcher
   */
  public static $instance;

  /**
   * Returns the instance of WU_Domain_Seller_Action_Dispatcher
   * 
   * @since 0.0.1
   * @return WU_Domain_Seller_Action_Dispatcher A WU_Domain_Seller_Action_Dispatcher instance
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
     * Add the Settings to the Domain Mapping and SSL options
     * @since 0.0.1
     */
    add_filter('wu_settings_section_domain_mapping', array($this, 'add_domain_seller_settings'));

    /**
     * Add Lookup endpoint
     * @since 0.0.1
     */
    add_action('wp_ajax_wu_domain_selling', array($this, 'dispatch'));
    add_action('wp_ajax_nopriv_wu_domain_selling', array($this, 'dispatch'));

    /**
     * Register the domain by the end of the signup
     */
    add_action('wp_ultimo_registration', array($this, 'register_domain_after_signup'), 9, 4);

    /**
     * When the first payment is received, process the domain order
     */
    add_action('wp_ultimo_payment_completed', array($this, 'process_domain_purchase_after_signup'), 9, 2);

    /**
     * Check the DNS settings of the domain to know if we should turn the mapping on or not
     */
    add_action('init', array($this, 'check_dns_settings_and_activate_domain'), 20);

  } // end construct;

  /**
   * Logs a message to log files
   *
   * @since 0.0.1
   * @param string $msg
   * @return boolean
   */
  public function log($msg) {

    $provider = $this->get_provider();

    return WU_Logger::add("domain-registration-{$provider->id}", $msg);

  } // end log;

  /**
   * Creates the domain purchase on the provider
   *
   * @since 0.0.1
   * @param integer $site_id
   * @param integer $user_id
   * @param array   $transient
   * @param WU_Plan $plan
   * @return void
   */
  public function register_domain_after_signup($site_id, $user_id, $transient, $plan) {

    if (WU_Ultimo_Domain_Seller()->has_domain_selling_feature() && $plan && $plan->domain_registration) {

      $site = wu_get_site($site_id);

      if (!$site) return false;

      $domain = isset($transient['selected-domain']) ? $transient['selected-domain'] : false;

      /**
       * Saves it on the site itself
       */
      $site->set_meta('selected_domain', $domain);

      if (!$domain) return false;

      $this->log(
        sprintf(__('Domain %s saved to site ID %s', 'wu-domain-seller'), $domain, $site_id)
      );

      $register_data = array(
        'domain'              => $domain,
        'custom_nameservers'  => 0,
        'custom_tech_contact' => 0,
        'period'              => WU_Settings::get_setting('opensrs_period', 1),
        'auto_renew'          => WU_Settings::get_setting('opensrs_autorenew', 0),
        'f_whois_privacy'     => WU_Settings::get_setting('opensrs_whois', 0),
        'handle'              => WU_Settings::get_setting('opensrs_handle', 'save'),
        'reg_username'        => $transient['user_name'],
        'reg_password'        => str_pad($transient['user_name'], 10, '0'),
        'reg_type'            => 'new',
        'dns_template'        => 'wp-ultimo',
        'personal'            => array(
          'email'              => get_network_option(null, 'admin_email'),
          'first_name'         => WU_Settings::get_setting('domain_selling_contact_first_name'),
          'last_name'          => WU_Settings::get_setting('domain_selling_contact_last_name'),
          'org_name'           => WU_Settings::get_setting('domain_selling_contact_org_name'),
          'phone'              => WU_Settings::get_setting('domain_selling_contact_phone'),
          'address1'           => WU_Settings::get_setting('domain_selling_contact_address1'),
          'city'               => WU_Settings::get_setting('domain_selling_contact_city'),
          'state'              => WU_Settings::get_setting('domain_selling_contact_state'),
          'country'            => WU_Settings::get_setting('domain_selling_contact_country'),
          'postal_code'        => WU_Settings::get_setting('domain_selling_contact_postal_code'),
        ),
      );

      /**
       * Gets the active provider to be used
       */
      $provider = $this->get_provider();

      /**
       * Sends this to the provider
       */
      $register_domain = $provider->register_domain($register_data);

      /**
       * Check the results
       */
      if ($register_domain['success'] && isset($register_domain['data']['id'])) {

        /**
         * Get the order ID for later. We'll need it to activate the domain
         */
        $order_id = $register_domain['data']['id'];

        /**
         * Save the order ID and set the status
         */
        $status = WU_Settings::get_setting('opensrs_handle', 'save') == 'save' ? 'pending' : 'waiting_dns_propagration';
        $site->set_meta('opensrs_order_id', $order_id);
        $site->set_meta('opensrs_order_status', $status);

        $this->log(
          sprintf(__('Order created on OpenSRS with id %s and status %s, domain: %s', 'wu-domain-seller'), $order_id, $status, $domain)
        );

        if ($status == 'process') {

          /**
           * Add the mapping as inactive
           */
          $this->add_mapping_as_inactive($site_id, $domain);

          $this->log(
            sprintf(__('Adding domain %s as an inactive mapping to site ID %s. Waiting for DNS propagation to occur.', 'wu-domain-seller'), $domain, $site_id)
          );

        } // end if;

        return true;

      } else {

        $this->log(
          sprintf(__('Error registering domain %s to site ID %s: $s', 'wu-domain-seller'), $domain, $site_id, $register_domain['data'])
        );

      } // end if;

    } // end if;

    return false;
     
  } // end register_domain_after_signup;

  /**
   * Once we receive the first payment, we need to commit the domain purchase order
   *
   * @since 0.0.1
   * @param integer $user_id
   * @param string $gateway_id
   * @return boolean
   */
  public function process_domain_purchase_after_signup($user_id, $gateway_id) {

    $subscription = wu_get_subscription($user_id);

    if (!$subscription) return false;

    $sites = $subscription->get_sites_ids();

    /**
     * Lopp all the user sites to make sute we validate all domains needing that
     */
    foreach($sites as $site_id) {

      $site = wu_get_site($site_id);

      if (!$site) continue;

      /**
       * Check if there is a domain and if it is pending
       */
      $domain       = $site->get_meta('selected_domain');
      $order_id     = $site->get_meta('opensrs_order_id');
      $order_status = $site->get_meta('opensrs_order_status');

      if ($domain && $order_id && $order_status == 'pending') {

        /**
         * Gets the active provider to be used
         */
        $provider = $this->get_provider();

        /**
         * Sends this to the provider
         */
        $activate_domain = $provider->activate_domain(array(
          'order_id'       => $order_id,
          'approver_email' => get_network_option(null, 'admin_email'),
          'dv_auth_method' => 'Email',
        ));

        /**
         * Add the mapping as inactive
         */
        $this->add_mapping_as_inactive($site_id, $domain);

        /**
         * Update the Domain Status
         */
        $site->set_meta('opensrs_order_status', 'waiting_dns_propagration');

        $this->log(
          sprintf(__('Adding domain %s as an inactive mapping to site ID %s. Waiting for DNS propagation to occur.', 'wu-domain-seller'), $domain, $site_id)
        );

      } // end if;

    } // end foreach;

    return false;

  } // end process_domain_purchase_after_signup;

  /**
   * Checks the mapped domain to see if the DNS records match the network IP Address.
   * If that's the case, sets the domain mapping as active.
   *
   * @since 0.0.1
   * @return void
   */
  public function check_dns_settings_and_activate_domain() {
    
    /**
     * Get the Site
     */
    $site = wu_get_current_site();

    if (!$site) return;

    /**
     * Check if there is a domain and if it is pending
     */
    $domain       = $site->get_meta('selected_domain');
    $order_id     = $site->get_meta('opensrs_order_id');
    $order_status = $site->get_meta('opensrs_order_status');

    // if (true) {
    if ($domain && $order_id && $order_status == 'waiting_dns_propagration') {

      $site_ip = WU_Settings::get_setting('network_ip') ? WU_Settings::get_setting('network_ip') : WU_Domain_Mapping::get_ip_address();

      /**
       * DNS Records match up
       */
      if ( gethostbyname($domain) == $site_ip ) {

        /**
         * Update the status to completed
         */
        $site->set_meta('opensrs_order_status', 'completed');

        /**
         * Set the domain, now as active =)
         */
        $site->set_custom_domain($domain, true);

        $this->log(
          sprintf(__('DNS propagation for domain %s and site ID %s over, activating domain mapping', 'wu-domain-seller'), $domain, $site->ID)
        );

      } // end if;

    } // end if;

    return false;

  } // end check_dns_settings_and_activate_domain;

  /**
   * Adds a domain mapping on the site as inactive, while we don't have DNS confirmation
   *
   * @since 0.0.1
   * @param integer $site_id
   * @param string  $domain
   * @return boolean
   */
  public function add_mapping_as_inactive($site_id, $domain) {

    $site = wu_get_site($site_id);

    return $site->set_custom_domain($domain, false);

  } // end add_mapping_as_inactive;

  /**
   * List of allowed actions to be sent to the dispatcher
   *
   * @since 0.0.1
   * @return array
   */
  public function get_valid_actions() {

    return apply_filters('wu_domain_selling_get_valid_actions', array(
      'test_connectivity',
      'lookup_domain',
      'register_domain',
      'get_domain_dns_status',
    ));

  } // end get_valid_actions;

  /**
   * Get the currently active provider
   *
   * @since 0.0.1
   * @return WU_Domain_Seller_Base|false
   */
  public function get_provider() {

    $active_provider_id = WU_Settings::get_setting('domain_selling_provider', false);

    return $active_provider_id ? wu_get_domain_seller_provider($active_provider_id) : false;

  } // end get_provider;

  /**
   * Returns request parameters or false if they are not set
   *
   * @since 0.0.1
   * @param string $param REQUEST param to be returned
   * @return mixed
   */
  public function get_param($param) {

    return isset($_REQUEST[ $param ]) ? $_REQUEST[ $param ] : false;

  } // end get_param;

  /**
   * The Dispatcher
   * 
   * Takes in the action, check if that is a valid action and routes that to the provider
   * Also checks for referers and add do_action hooks to allow developers to do extra stuff
   *
   * @since 0.0.1
   * @return void
   */
  public function dispatch() {
    
    /**
     * Check for feature before moving on
     */
    if (!WU_Ultimo_Domain_Seller()->has_domain_selling_feature()) return;

    /**
     * Gets the active provider to be used
     */
    $provider = $this->get_provider();

    if ( $this->get_param('do') && in_array($this->get_param('do'), $this->get_valid_actions()) && $provider ) {

      /**
       * Get the action we want to perform
       */
      $do = $this->get_param('do');

      /**
       * Verify Nonce
       */
      if (!wp_verify_nonce($this->get_param('wpnonce'), "wu_domain_selling_{$do}_nonce")) return;

      /**
       * Run hooks before the dispatcher hands this to the provider
       */
      do_action('wu_domain_selling_before_action', $do, $this);

      /**
       * Sends this to the provider
       */
      $results = $provider->{$do}($this);

      /**
       * Run hooks after the dispatcher hands this to the provider
       */
      do_action('wu_domain_selling_after_action', $do, $this);

      /**
       * Return the data
       */
      wp_send_json($results);

      exit;

    } // end if;

  } // end dispatch;

  /**
   * Returns an array with the available providers to select
   *
   * @since 0.0.1
   * @return array Array containing list of id => Provider Name
   */
  public function get_reseller_options() {

    $provider_list = array_map(function($provider) {

      return $provider['name'];

    }, wu_get_domain_seller_providers());

    return array_merge(array(
      '' => __('Select Provider', 'wu-domain-seller')
    ), $provider_list);

  } // end get_reseller_options;

  /**
   * Filters the WP Ultimo settings to add the domain seller options.
   * Loops through the available providers to inject their fields as well
   *
   * @since 0.0.1
   * @return array
   */
  public function get_providers_settings() {

    $providers = wu_get_domain_seller_providers();

    $settings = array();

    foreach($providers as $provider_id => $provider_name) {

      $provider = wu_get_domain_seller_provider($provider_id);

      // var_dump($provider); die;

      if ($provider) {

        $settings = array_merge($settings, $provider->settings());

      } // end if;
      
    } // end foreach;

    return $settings;

  } // end get_providers_settings;

  /**
   * Filters the WP Ultimo settings to add the domain seller options.
   * Loops through the available providers to inject their fields as well
   *
   * @since 0.0.1
   * @return array
   */
  public function add_domain_seller_settings($settings) {

    $super_admin_login = array_values(get_super_admins())[0];

    $user = get_user_by('login', $super_admin_login);

    $fields = array(

      'domain_seller_header' => array(
        'title'   => __('Domain Registration Options', 'wu-domain-seller'),
        'desc'    => __('Configure domain registration during signup.', 'wu-domain-seller'),
        'type'    => 'heading',
        'require' => array('enable_domain_mapping' => 1),
      ),

      'enable_domain_selling' => array(
        'title'   => __('Enable Domain Registration', 'wu-domain-seller'),
        'desc'    => __('Do you want to enable domain registration for your clients? You will need to enable this feature on each of the plans individually.', 'wu-domain-seller'),
        'tooltip' => __('You can select which plans can have access to this feature.', 'wu-domain-seller'),
        'type'    => 'checkbox',
        'default' => false,
        'require' => array('enable_domain_mapping' => 1),
      ),

      'keep_domain_selling_step' => array(
        'title'   => __('Display Domain Registration Step for all Plans', 'wu-domain-seller'),
        'desc'    => __('If enabled, this option will display the domain register step for all plans, even when they do not support domain registration (in this case, an error message will be displayed).', 'wu-domain-seller'),
        'tooltip' => '',
        'type'    => 'checkbox',
        'default' => true,
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_pre_text' => array(
        'title'   => __('Previous Step Helper Text', 'wu-domain-seller'),
        'desc'    => __('Allows you to edit the message that is displayed to the user on the Site Title/Subdomain step. Leave blank to hide.', 'wu-domain-seller'),
        'tooltip' => '',
        'type'    => 'textarea',
        'html_attr' => array('rows' => 2),
        'default' => __("Don't worry! You'll be able to select a custom domain on the next step!", 'wu-domain-seller'),
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_helper_text' => array(
        'title'   => __('Domain Search Step Helper Text', 'wu-domain-seller'),
        'desc'    => __('Allows you to edit the message that is displayed to the user on the Domain Search step. Leave blank to hide.', 'wu-domain-seller'),
        'tooltip' => '',
        'type'    => 'textarea',
        'html_attr' => array('rows' => 2),
        'default' => __("Let's pick an awesome domain name for your site!", 'wu-domain-seller'),
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_info_header' => array(
        'title'   => __('Domain Registration Contact Options', 'wu-domain-seller'),
        'desc'    => __('Configure domain registration during signup.', 'wu-domain-seller'),
        'type'    => 'heading',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_first_name' => array(
        'title'         => __('First Name', 'wu-domain-seller'),
        'placeholder'   => 'John',
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => $user->first_name,
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_last_name' => array(
        'title'         => __('Last Name', 'wu-domain-seller'),
        'placeholder'   => 'Smith',
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => $user->last_name,
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_org_name' => array(
        'title'         => __('Organization Name', 'wu-domain-seller'),
        'placeholder'   => 'WP Ultimo, Inc.',
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => get_network()->site_name,
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_phone' => array(
        'title'         => __('Phone', 'wu-domain-seller'),
        'placeholder'   => __('+1.4165550123x1902', 'wu-domain-seller'),
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => '',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_address1' => array(
        'title'         => __('Address', 'wu-domain-seller'),
        'placeholder'   => __('32 Oak Street', 'wu-domain-seller'),
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => '',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_city' => array(
        'title'         => __('City', 'wu-domain-seller'),
        'placeholder'   => __('Santa Clara', 'wu-domain-seller'),
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => '',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_state' => array(
        'title'         => __('State', 'wu-domain-seller'),
        'placeholder'   => __('CA', 'wu-domain-seller'),
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => '',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_country' => array(
        'title'         => __('Country', 'wu-domain-seller'),
        'placeholder'   => __('US', 'wu-domain-seller'),
        'type'          => 'select',
        'desc'          => '',
        'tooltip'       => '',
        'options'       => WU_Settings::get_countries(),
        'default'       => 'US',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_contact_postal_code' => array(
        'title'         => __('Postal Code', 'wu-domain-seller'),
        'placeholder'   => __('90210', 'wu-domain-seller'),
        'type'          => 'text',
        'desc'          => '',
        'tooltip'       => '',
        'default'       => '',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_provider_header' => array(
        'title'   => __('Domain Registration Provider Options', 'wu-domain-seller'),
        'desc'    => __('Configure specific domain registration provider options.', 'wu-domain-seller'),
        'type'    => 'heading',
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),
      ),

      'domain_selling_provider' => array(
        'title'         => __('Domain Registration Provider', 'wu-domain-seller'),
        'desc'          => __('Select which domain registration provider you want to use.', 'wu-domain-seller'),
        'tooltip'       => '',
        'default'       => '',
        'type'          => 'select',
        'options'       => $this->get_reseller_options(),
        'require'       => array(
          'enable_domain_mapping' => 1,
          'enable_domain_selling' => 1,
        ),

      ),

    );

    return array_merge($settings, $fields, $this->get_providers_settings());

  } // end add_domain_seller_settings;

} // end class WU_Domain_Seller_Action_Dispatcher;

WU_Domain_Seller_Action_Dispatcher::get_instance();

endif;