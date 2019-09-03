<?php
/**
 * Pricing table for blocks
 *
 * Base class for the modals to be extended by other data elements
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Blocks/Pricing_Table
 * @version     0.0.1
*/

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Pricing Table Block class.
 */
class WU_Block_Pricing_Table extends WU_Block {

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 0.0.1
	 * @uses {wp-blocks} for block type registration & related functions.
	 * @uses {wp-element} for WP Element abstraction — structure of blocks.
	 * @uses {wp-i18n} to internationalize the block's text.
	 * @uses {wp-editor} for WP editor styles.
	 * @return void
	 */
	public function block_init() {

		if (!WP_Ultimo_Blocks()->has_gutenberg_available()) return;

		$shortcodes = new WU_Shortcodes;

		// Register our block, and explicitly define the attributes we accept.
		register_block_type('wp-ultimo/wp-ultimo-block-pricing-table', array(
			'attributes' => array(
				'primary_color'     => array(
					'default' => WU_Settings::get_setting('primary-color', '#00a1ff'),
					'type'    => 'string',
				),
				'accent_color' => array(
					'default' => WU_Settings::get_setting('accent-color', '#00a1ff'),
					'type'    => 'string',
				),
				'plan_id' => array(
					'default' => 'all',
					'type'    => 'string',
				),
				'default_pricing_option' => array(
					'default' => 1,
					'type'    => 'integer',
				),
				'show_selector' => array(
					'default' => true,
					'type'    => 'boolean',
				),
			),
			'editor_script'   => 'wp-ultimo-blocks', // The script name we gave in the wp_register_script() call.
			'render_callback' => array($shortcodes, 'pricing_table'),
		));

	} // end block_init;

} // end class WU_Block_Pricing_Table;

new WU_Block_Pricing_Table;