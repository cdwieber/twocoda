<?php
/**
 * Template List for blocks
 *
 * Base class for the modals to be extended by other data elements
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Blocks/Template_List
 * @version     0.0.1
*/

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Template List Block class.
 */
class WU_Block_Template_List extends WU_Block {

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
		register_block_type('wp-ultimo/wp-ultimo-block-template-list', array(
			'attributes' => array(
				'show_title' => array(
					'default' => true,
					'type'    => 'boolean',
				),
				'show_filters' => array(
					'default' => true,
					'type'    => 'boolean',
				),
				'cols' => array(
					'default' => 3,
					'type'    => 'int',
				),
				'templates' => array(
					'default' => '0',
					'type'    => 'string',
				),
				'select_templates' => array(
					'default' => false,
					'type'    => 'boolean',
				),
			),
			'editor_script'   => 'wp-ultimo-blocks', // The script name we gave in the wp_register_script() call.
			'render_callback' => array($shortcodes, 'templates_list'),
		));

	} // end block_init;

} // end class WU_Block_Template_List;

new WU_Block_Template_List;