<?php
/**
 * Domain Seller OpenSRS Implementation
 *
 * This implements the connection between WP Ultimo and OpenSRS
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Domain_Seller/OpenSRS
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WU_Domain_Seller_OpenSRS')) :

class WU_Domain_Seller_OpenSRS extends WU_Domain_Seller_Base {

  /**
   * Holds the ID for this particular provider
   *
   * @since 0.0.1
   * @var string $id
   */
  public $id = 'opensrs';

  /**
   * Provider name, e.g. OpenSRS
   *
   * @since 0.0.1
   * @var string $provider_name
   */
  public $provider_name = 'OpenSRS';

  /**
   * Setup
   * 
   * Defines the constants that OpenSRS needs to connect to their API servers
   *
   * @since 0.0.1
   * @return void
   */
  public function setup() {

    /**
     * Bail if we have already set this up
     */
    if (defined('OSRS_USERNAME')) return;

    // Definitions

    // Setting default path to the same directory this file is in
    define('DS', DIRECTORY_SEPARATOR);
    define('PS', PATH_SEPARATOR);
    define('CRLF', "\r\n");

    define('OPENSRSURI', dirname(__FILE__));

    /**
    * OpenSRS Domain service directories include provisioning, lookup, and dns
    */

    define('OPENSRSDOMAINS', OPENSRSURI . DS . 'domains');

    /**
    * OpenSRS trust service directory
    */
    define('OPENSRSTRUST', OPENSRSURI . DS . 'trust');

    /**
    * OpenSRS publishing service directory
    */
    define('OPENSRSPUBLISHING', OPENSRSURI . DS . 'publishing');

    /**
    * OpenSRS email service (OMA) directory
    */
    define('OPENSRSOMA', OPENSRSURI . DS . 'OMA');

    /**
    * OpenSRS email service (APP) directory
    */
    define('OPENSRSMAIL', OPENSRSURI . DS . 'mail');

    define('OPENSRSFASTLOOKUP', OPENSRSURI . DS . 'fastlookup');

    /** 
    * OpenSRS reseller username
    */
    define('OSRS_USERNAME', WU_Settings::get_setting('opensrs_username'));

    /** 
    * OpenSRS reseller private Key. Please generate a key if you do not already have one.
    */
    define('OSRS_KEY', WU_Settings::get_setting('opensrs_api_key'));

    /**
    * OpenSRS default encryption type => ssl, sslv2, sslv3, tls
    */
    define('CRYPT_TYPE', 'ssl');

    /** 
    * OpenSRS domain service API url.
    * LIVE => rr-n1-tor.opensrs.net, TEST => horizon.opensrs.net
    */
    define('OSRS_HOST', WU_Settings::get_setting('opensrs_sandbox', true) ? 'horizon.opensrs.net' : 'rr-n1-tor.opensrs.net');

    /** 
    * OpenSRS API SSL port
    */
    define('OSRS_SSL_PORT', '55443');

    /** 
    * OpenSRS protocol. XCP or TPP.
    */
    define('OSRS_PROTOCOL', 'XCP');

    /** 
    * OpenSRS version
    */
    define('OSRS_VERSION', 'XML:0.1');

    /** 
    * OpenSRS domain service debug flag
    */
    define('OSRS_DEBUG', defined('WP_DEBUG') && WP_DEBUG);

    /** 
    * OpenSRS API fastlookup port`
    */
    define('OSRS_FASTLOOKUP_PORT', '51000');

    /**
     * Loads the opensrs APIs
     * 
     * @since 0.0.1
     */
    require_once WU_Ultimo_Domain_Seller()->path('vendor/autoload.php');

  } // end setup;

  /**
   * Returns the setup instructions for OpenSRS
   *
   * @since 0.0.1
   * @return string Instructions for setup
   */
  public function get_setup_instructions() {

    $opensrs_tutorial_link = "https://help.opensrs.com/hc/en-us/articles/203858966-API-Key-IP-Access";

    return sprintf(__('In order for OpenSRS to work, you\'ll need to whitelist the network IP address on your OpenSRS management panel (test and live). You can find more instructions on the %s. <br>Your network\'s IP Address is <strong>%s</strong>', 'wu-domain-seller'), '<a href="'. $opensrs_tutorial_link .'">'. __('OpenSRS tutorial page', 'wu-domain-seller') .'</a>', WU_Domain_Mapping::get_ip_address());

  } // end get_setup_instructions;

  /**
   * The settings declaration for this provider
   * 
   * Adds the specific fields required for OpenSRS, like API key, username, etc
   *
   * @since 0.0.1
   * @see WU_Settings
   * @return array
   */
  public function settings() {

    return array(

      'opensrs' => array(
        'title'          => __('OpenSRS Options', 'wu-domain-seller'),
        'desc'           => __('Configure the OpenSRS integration.', 'wu-domain-seller'),
        'type'           => 'heading',
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_sandbox'  => array(
        'title'          => __('OpenSRS Sandbox', 'wu-domain-seller'),
        'desc'           => __('Enable/Disable the test mode.', 'wu-domain-seller'),
        'tooltip'        => __('If you use the sandbox mode, keep in mind that the management panel is different from the live one. The same is valid for the API key and IP whitelist. Be sure to get the integration info from the right OpenSRS environment.', 'wu-domain-seller'),
        'type'           => 'checkbox',
        'default'        => true,
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_username' => array(
        'title'          => __('OpenSRS Reseller Username', 'wu-domain-seller'),
        'desc'           => __('Your OpenSRS username', 'wu-domain-seller'),
        'default'        => '',
        'type'           => 'text',
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_api_key' => array(
        'title'          => __('OpenSRS API Key', 'wu-domain-seller'),
        'desc'           => __('Your OpenSRS API Key', 'wu-domain-seller'),
        'default'        => '',
        'type'           => 'text',
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_ip_instructions' => array(
        'title'         => __('OpenSRS IP Instructions', 'wu-domain-seller'),
        'type'          => 'note',
        'desc'          => $this->get_setup_instructions(),
        'require'       => $this->get_setting_requirements(),
      ),

      'opensrs_tlds' => array(
        'title'         => __('Allowed TLDs', 'wu-domain-seller'),
        'desc'          => sprintf(__('Select the TLDs you want to make available for your end users to choose from. Keep in mind that due to technical limitations, WP Ultimo does not yet support all TLDs available on OpenSRS. Each TLD has a different price. Check the %spricing page%s on OpenSRS for more information.', 'wu-domain-seller'), '<a href="https://opensrs.com/services/domains/domain-pricing/" target="_blank">', '</a>'),
        'tooltip'       => __('', 'wu-domain-seller'),
        'placeholder'   => __('.com, .org, .net', 'wu-domain-seller'),
        'type'          => 'select2',
        'default'       => array('.com', '.net', '.org'),
        'options'       => include WU_Ultimo_Domain_Seller()->path('inc/data/tld.php'),
        'require'       => $this->get_setting_requirements(),
      ),

      'opensrs_handle' => array(
        'title'         => __('Order Bahavior', 'wu-domain-seller'),
        'desc'          => __('Select if you want the domain to be purchased right after the signup is over, or if you wish to save the order as a draft and only execute it (pay for it) when you receive the first payment from your client.', 'wu-domain-seller'),
        'tooltip'       => '',
        'type'          => 'select',
        'default'       => 'save',
        'options'       => array(
          'save'         => __('Only finalize order after first payment from user', 'wu-domain-seller'),
          'process'      => __('Finalize order right after the registration is over', 'wu-domain-seller'),
        ),
        'require'       => $this->get_setting_requirements(),
      ),

      'opensrs_period'  => array(
        'title'          => __('Registration Period', 'wu-domain-seller'),
        'desc'           => __('The length of the registration period in years.', 'wu-domain-seller'),
        'tooltip'        => '',
        'type'           => 'number',
        'default'        => 1,
        'html_attr'      => array(
          'min'           => 1,
          'max'           => 10,
        ),
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_autorenew'  => array(
        'title'          => __('Auto-renew Domains?', 'wu-domain-seller'),
        'desc'           => __('If enabled, domains will be registered with a auto-renew flag.', 'wu-domain-seller'),
        'tooltip'        => '',
        'type'           => 'checkbox',
        'default'        => false,
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_whois'  => array(
        'title'          => __('Enable WHOIS Privacy Protection?', 'wu-domain-seller'),
        'desc'           => __('If enabled, domains will be registered with the WHOIS Privacy Protection flag on OpenSRS. This adds an extra cost to the domain purchase value.', 'wu-domain-seller'),
        'tooltip'        => '',
        'type'           => 'checkbox',
        'default'        => false,
        'require'        => $this->get_setting_requirements(),
      ),

      'opensrs_test_connectivity' => array(
        'title'         => __('Test Connectivity', 'wu-domain-seller'),
        'desc'          => __('Clicking the button will try to send an API request to the provider\'s server to see if your settings are correctly set up.', 'wu-domain-seller'),
        'label'         => __('Send Test API Call', 'wu-domain-seller'),
        'tooltip'       => '',
        'type'          => 'ajax_button',
        'action'        => "wu_domain_selling&do=test_connectivity&wpnonce=" . wp_create_nonce('wu_domain_selling_test_connectivity_nonce'),
        'require'       => $this->get_setting_requirements(),
      ),

    );

  } // end settings;

  /**
   * Test Connectivity
   * 
   * Should try to connect to the registrar API servers and return true if it was successful
   *
   * @since 0.0.1
   * @param WU_Domain_Seller_Action_Dispatcher $dispatcher
   * @return boolean
   */
  public function test_connectivity($dispatcher) {

    $domain = 'testdomain.com';

    $data = array(
      'func' => 'allinonedomain',
      'data' => array(
        'domain' => $domain,
      ),
    );

    try {

      $request = new opensrs\Request();

      $response = $request->process('array', $data);

      return (array(
        'success' => true,
        'message' => __('Successfully able to connect to OpenSRS!', 'wu-domain-seller'),
        'data'    => array(),
      ));

    } catch (Exception $e) {

      return (array(
        'success' => false,
        'message' => $e->getMessage(),
        'data'    => array(),
      ));

    } // end try-catch;

  } // end test_connectivity;

  /**
   * Returns the list of allowed TLDs for OpenSRS
   *
   * @since 0.0.1
   * @return array
   */
  public function get_tld_list() {

    $tld_list = WU_Settings::get_setting('opensrs_tlds', array());

    return apply_filters('wu_domain_seller_opensrs_get_tld_list', implode(';', $tld_list), $tld_list);

  } // end 

  /**
   * Lookup Domain
   * 
   * Should search for a domain and return if its available or not.
   * Can also return suggestions for other domains if they are available.
   *
   * @since 0.0.1
   * @param WU_Domain_Seller_Action_Dispatcher $dispatcher
   * @return array
   */
  public function lookup_domain($dispatcher) {

    $domain = $dispatcher->get_param('domain');

    $data = array(
      'func' => 'allinonedomain',
      'data' => array(
        'domain'     => $domain,
        'selected'   => $this->get_tld_list(),
        'alldomains' => $this->get_tld_list(),
      ),
    );

    try {

      $request = new opensrs\Request();

      $response = $request->process('array', $data);

      return (array(
        'success' => true,
        'data'    => $this->process_results( $response->resultRaw['lookup']['items'] ),
      ));

    } catch (opensrs\Exception $e) {

      return (array(
        'success' => false,
        'data'    => $e->getMessage(),
      ));

    } // end try-catch;

  } // end lookup_domain;

  public function process_results($results) {

    return array_map(function($item) {

      return array(
        'domain'    => $item['domain'],
        'available' => $item['status'] == 'available' 
      );

    }, $results);

  } // end process_results;

  /**
   * Register Domain
   * 
   * Should send a register call to the registrar API and try to register the domain, returning the results.
   *
   * @since 0.0.1
   * @param array $data
   * @return array
   */
  public function register_domain($data) {

    /**
     * Data array Example for OpenSRS
     * array(
     *  'domain'              => 'domain.com',
     *  'custom_nameservers'  => 0,
     *  'custom_tech_contact' => 1,
     *  'period'              => 1,
     *  'handle'              => 'process',
     *  'reg_username'        => 'testeuser'.rand(100000000, 99999999),
     *  'reg_password'        =>  rand(100000000000, 99999999999),
     *  'reg_type'            => 'new',
     *  'personal'            => array(
     *    'country'            => 'US',
     *    'address1'           => '32 Oak Street',
     *    'address3'           => 'Owner',
     *    'address2'           => 'Suite 500',
     *    'org_name'           => 'Example Inc.',
     *    'phone'              => '+1.4165550123x1902',
     *    'state'              => 'CA',
     *    'last_name'          => 'Ottway',
     *    'email'              => 'ottway@example.com',
     *    'city'               => 'SomeCity',
     *    'postal_code'        => '90210',
     *    'fax'                => '+1.4165550124',
     *    'first_name'         => 'Owen',
     *   ),
     * ),
     */

    $data = array(
      'func' => 'provswregister',
      'data' => $data,
    );

    try {

      $request = new opensrs\Request();

      $response = $request->process('array', $data);

      return (array(
        'success' => true,
        'data'    => $response->resultRaw,
      ));

    } catch (\Exception $e) {

      return (array(
        'success' => false,
        'data'    => $e->getInfo(),
        'message' => $e->getMessage(),
      ));

    } // end try-catch;

  } // end register_domain;

  /**
   * Activate Domain
   * 
   * Activate the domain purchase on the backend at OpenSRS. This is what in fact makes the purchase and 
   * deducts the money from your account balance
   *
   * @since 0.0.1
   * @param array $data
   * @return array
   */
  public function activate_domain($data) {

    $data = array(
      'func' => 'provprocesspending',
      'data' => $data,
    );

    try {

      $request = new opensrs\Request();

      $response = $request->process('array', $data);

      return (array(
        'success' => true,
        'data'    => $response->resultRaw,
      ));

    } catch (\Exception $e) {

      return (array(
        'success' => false,
        'data'    => $e->getInfo(),
        'message' => $e->getMessage(),
      ));

    } // end try-catch;

  } // end activate_domain;

  /**
   * Add DNS Entries
   * 
   * Adds the DNS entry to a given domain.
   * TODO: This is not working currently, not sure why.
   *
   * @since 0.0.1
   * @param array $data
   * @return array
   */
  public function add_dns_to_domain($data) {

    $data = array(
      'func' => 'dnscreate',
      'data' => $data,
    );

    try {

      $request = new opensrs\Request();

      $response = $request->process('array', $data);

      return (array(
        'success' => true,
        'data'    => $response->resultRaw,
      ));

    } catch (\Exception $e) {

      return (array(
        'success' => false,
        'data'    => $e->getInfo(),
        'message' => $e->getMessage(),
      ));

    } // end try-catch;

  } // end add_dns_to_domain;

} // end class WU_Domain_Seller_OpenSRS;

/**
 * Register the provider
 */
wu_register_domain_seller_provider('opensrs', 'OpenSRS', 'WU_Domain_Seller_OpenSRS');

endif;