<?php
/**
 * Block Base Class
 *
 * Base class for the blocks WP Ultimo implement =)
 * Blocks are the FUTURE, man! No joke
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Blocks
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * WU_Block class.
 */
class WU_Block {

  /**
   * Register the hooks
   *
   * @since 0.0.1
   */
  public function __construct() {

    /**
     * Init the block
     * 
     * @since 0.0.1
     */
    add_action('init', array($this, 'block_init'), 15);

  } // end construct;

  /**
   * Block Init
   *
   * @since 0.0.1
   * @return void
   */
  public function block_init() { } // end block_init;

} // end class WU_Block;