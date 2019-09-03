<?php
/**
 * Domain Seller Example Implementation
 *
 * Example of what a simple implementation of a different provider would look like.
 * Use this as an starting point if you wish to implement a different provider =)
 * In case of any doubts, hit me at arindo@wpultimo.com
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Domain_Seller/Example
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WU_Domain_Seller_Example')) :

class WU_Domain_Seller_Example extends WU_Domain_Seller_Base {

  /**
   * Holds the ID for this particular provider
   *
   * @since 0.0.1
   * @var string $id
   */
  public $id = 'example';

  /**
   * Provider name, e.g. OpenSRS
   *
   * @since 0.0.1
   * @var string $provider_name
   */
  public $provider_name = 'Example Provider';

  /**
   * Setup
   * 
   * Defines the constants that OpenSRS needs to connect to their API servers
   *
   * @since 0.0.1
   * @return void
   */
  public function setup() {

  } // end setup;

  /**
   * The settings declaration for this provider
   * 
   * Adds the specific fields required for OpenSRS, like API key, username, etc
   *
   * @since 0.0.1
   * @see WU_Settings
   * @return array
   */
  public function settings() {} // end settings;

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

  } // end test_connectivity;

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

  } // end lookup_domain;

  /**
   * Register Domain
   * 
   * Should send a register call to the registrar API and try to register the domain, returning the results.
   *
   * @since 0.0.1
   * @param WU_Domain_Seller_Action_Dispatcher $dispatcher
   * @return array
   */
  public function register_domain($dispatcher) {

  } // end register_domain;

} // end class WU_Domain_Seller_Example;

/**
 * Register the provider
 */
wu_register_domain_seller_provider('example', 'Example provider', 'WU_Domain_Seller_Example');

endif;