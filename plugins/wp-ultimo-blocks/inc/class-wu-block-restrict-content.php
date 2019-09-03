<?php
/**
 * Restrict Content for blocks
 *
 * Base class for the modals to be extended by other data elements
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Blocks/Restrict_Content
 * @version     0.0.1
*/

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Restrict Content Block class.
 */
class WU_Block_Restrict_Content extends WU_Block {

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
		register_block_type('wp-ultimo/wp-ultimo-block-restrict-content', array(
			'attributes' => array(
				'plan_id' => array(
					'default' => 'all',
					'type'    => 'string',
				),
				'only_active' => array(
					'default' => true,
					'type'    => 'boolean',
				),
				'only_logged' => array(
					'default' => true,
					'type'    => 'boolean',
				),
				'exclude_trials' => array(
					'default' => false,
					'type'    => 'boolean',
				),
			),
			'editor_script'   => 'wp-ultimo-blocks', // The script name we gave in the wp_register_script() call.
			'render_callback' => array($shortcodes, 'restricted_content'),
		));

	} // end block_init;

} // end class WU_Block_Restrict_Content;

new WU_Block_Restrict_Content;