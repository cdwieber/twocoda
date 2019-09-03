<?php
/**
 * Domain Seller Base Class
 *
 * Model class that providers should extend to be available as an option.
 * To see how to implement a new provider, extending the domain selling capabilities,
 * Check the example provider at the class-wu-domain-seller-example.php
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Domain_Seller
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WU_Domain_Seller_Base')) :

class WU_Domain_Seller_Base {

  /**
   * Holds the ID for this particular provider
   *
   * @since 0.0.1
   * @var string $id
   */
  public $id = '';

  /**
   * Provider name, e.g. OpenSRS
   *
   * @since 0.0.1
   * @var string $provider_name
   */
  public $provider_name = '';

  /**
   * Makes sure we are only using one instance of the plugin
   *
   * @since 0.0.1
   * @var object WU_Ultimo_Domain_Seller
   */
  public static $instance;

  /**
   * Returns the instance of WU_Domain_Seller_Base
   * 
   * @since 0.0.1
   * @return object A WU_Domain_Seller_Base instance
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
     * Runs the setup method of the provider, 
     * like setting up credentials and so on
     */
    $this->setup();

  } // end construct;

  /**
   * Get the setting requirements for the fields.
   * 
   * This makes sure the fields declared on the settings portion of the provider
   * only get to appear on the screen when the provider is selected
   *
   * @since 0.0.1
   * @return array
   */
  public function get_setting_requirements() {

    return apply_filters('wu_domain_selling_get_setting_requirements', array(
      'enable_domain_mapping'   => 1,
      'enable_domain_selling'   => 1,
      'domain_selling_provider' => $this->id,
    ), $this->id, $this);

  } // end get_setting_requirements;

  /**
   * The settings declaration for this provider
   * 
   * Should return an array of WP Ultimo compatible fields.
   *
   * @since 0.0.1
   * @see WU_Settings
   * @return array
   */
  public function settings() {

    return array();

  } // end settings;

  /**
   * Setup
   * 
   * Allows classes extending this base class to setup 
   * things they need, like API keys, constants, etc
   *
   * @since 0.0.1
   * @return void
   */
  public function setup() {} // end setup;

  /**
   * Test Connectivity
   * 
   * Should try to connect to the registrar API servers and return true if it was successful
   *
   * @since 0.0.1
   * @param WU_Domain_Seller_Action_Dispatcher $dispatcher
   * @return boolean
   */
  public function test_connectivity($dispatcher) {} // end test_connectivity;

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
  public function lookup_domain($dispatcher) {} // end lookup_domain;

  /**
   * Results Processor
   * 
   * In order to display the domain options to the user on the front-end, during the signup,
   * we need to have the results of the lookup in a certain format.
   * 
   * Your provider implementation should implement this function.
   * 
   * The format for the lookup response is:
   * array(
   *   'success' => true,
   *   'data'    => array(
   *      array(
   *        'domain'   => 'domain.com.br', // string with the domain name
   *        'available => true             // boolean
   *      )
   *   )
   * )
   *
   * @since 0.0.1
   * @param array $results
   * @return array
   */
  public function process_results($results) {

    return $results;

  } // end process_results;

  /**
   * Register Domain
   * 
   * Should send a register call to the registrar API and try to register the domain, returning the results.
   *
   * @since 0.0.1
   * @param WU_Domain_Seller_Action_Dispatcher $dispatcher
   * @return array
   */
  // public function register_domain($dispatcher) {} // end register_domain;

  /**
   * Register Domain
   * 
   * Should send a register call to the registrar API and try to register the domain, returning the results.
   *
   * @since 0.0.1
   * @param WU_Domain_Seller_Action_Dispatcher $dispatcher
   * @return array
   */
  public function get_domain_dns_status($dispatcher) {} // end get_domain_dns_status;

} // end class WU_Domain_Seller_Base;

/**
 * Register Domain Seller Provider
 * 
 * Adds one class as an available provider for user selection
 *
 * @since 0.0.1
 * @param string $provider_id    ID of the provider, will be used to fetch this later. e.g. opensrs
 * @param string $provider_name  Name of the provider, e.g. OpenSRS
 * @param string $class_name     Name of the class. e.g. WU_Domain_Seller_OpenSRS
 * @return void
 */
function wu_register_domain_seller_provider($provider_id, $provider_name, $class_name) {

  global $wu_domain_seller_providers;

  $wu_domain_seller_providers = is_array($wu_domain_seller_providers) ? $wu_domain_seller_providers : array();

  $wu_domain_seller_providers[$provider_id] = array(
    'name'  => $provider_name,
    'class' => $class_name,
  );

} // end wu_register_domain_seller_provider;

/**
 * Get the list of available providers
 *
 * @since 0.0.1
 * @return array Array or the registered providers
 */
function wu_get_domain_seller_providers() {

  global $wu_domain_seller_providers;

  return is_array($wu_domain_seller_providers) ? $wu_domain_seller_providers : array();

} // end wu_get_domain_seller_provider;

/**
 * Get a specific provider by ID
 * 
 * Returns an instance of the class registered for that provider.
 *
 * @since 0.0.1
 * @param string $provider_id ID of the provider, will be used to fetch this later. e.g. opensrs
 * @return WU_Domain_Seller_Base
 */
function wu_get_domain_seller_provider($provider_id) {

  global $wu_domain_seller_providers;

  if (isset($wu_domain_seller_providers[ $provider_id ])) {

    if (class_exists( $wu_domain_seller_providers[ $provider_id ]['class'] )) {

      $class_name = $wu_domain_seller_providers[ $provider_id ]['class'];

      return new $class_name;

    } // end if;

  } // end if;

  return false;

} // end wu_get_domain_seller_provider;

endif;